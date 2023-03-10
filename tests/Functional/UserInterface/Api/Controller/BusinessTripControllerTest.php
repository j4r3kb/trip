<?php

declare(strict_types=1);

namespace App\Tests\UserInterface\Api\Controller;

use App\BusinessTrip\Domain\Entity\BusinessTrip;
use App\BusinessTrip\Domain\Entity\SubsistenceAllowance;
use App\BusinessTrip\Domain\Repository\BusinessTripRepository;
use App\BusinessTrip\Domain\Repository\SubsistenceAllowanceRepository;
use App\BusinessTrip\Domain\ValueObject\AllowancePerDay;
use App\BusinessTrip\Domain\ValueObject\BusinessTripDuration;
use App\Employee\Domain\Entity\Employee;
use App\Employee\Domain\Repository\EmployeeRepository;
use App\Employee\Domain\ValueObject\EmployeeId;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class BusinessTripControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;

    private ?string $employeeId = null;

    public function testReturns404WhenEmployeeDoesNotExist(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            sprintf('/employees/%s/business-trips', Uuid::v4()->toRfc4122()),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    'startDate' => '2020-01-01 12:00:00',
                    'endDate' => '2020-01-01 12:00:00',
                    'countryCode' => 'pl',
                ]
            )
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertResponseFormatSame('json');

        $this->client->request(
            Request::METHOD_GET,
            sprintf('/employees/%s/business-trips', Uuid::v4()->toRfc4122())
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertResponseFormatSame('json');
    }

    public function testReturns400WhenDateRangeIsInvalid(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            sprintf('/employees/%s/business-trips', $this->employeeId),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    'startDate' => '2020-01-01 12:00:00',
                    'endDate' => '2020-01-01 12:00:00',
                    'countryCode' => 'pl',
                ]
            )
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseFormatSame('json');
    }

    public function testReturns400WhenCountryCodeNotSupported(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            sprintf('/employees/%s/business-trips', $this->employeeId),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    'startDate' => '2020-01-01 12:00:00',
                    'endDate' => '2020-01-05 12:00:00',
                    'countryCode' => 'xx',
                ]
            )
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseFormatSame('json');
    }

    public function testReturns400WhenBusinessTripDurationsOverlap(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            sprintf('/employees/%s/business-trips', $this->employeeId),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    'startDate' => '2023-01-14 15:00:00',
                    'endDate' => '2023-01-18 12:00:00',
                    'countryCode' => 'pl',
                ]
            )
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseFormatSame('json');
    }

    public function testReturns201WhenInputDataIsValid(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            sprintf('/employees/%s/business-trips', $this->employeeId),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    'startDate' => '2023-01-15 08:00:00',
                    'endDate' => '2023-01-20 18:00:00',
                    'countryCode' => 'pl',
                ]
            )
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseFormatSame('json');
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertStringContainsString('_links', $content);
    }

    public function testReturns200WithListOfBusinessTrips(): void
    {
        $container = $this->getContainer();
        $businessTrip = BusinessTrip::create(
            EmployeeId::fromString($this->employeeId),
            'de',
            BusinessTripDuration::create(
                CarbonImmutable::parse('2023-01-15 08:00:00'),
                CarbonImmutable::parse('2023-01-20 16:00:00')
            ),
            AllowancePerDay::create(25, 'PLN')
        );
        $businessTripRepository = $container->get(BusinessTripRepository::class);
        $businessTripRepository->save($businessTrip);
        $container->get(EntityManagerInterface::class)->flush();

        $this->client->request(
            Request::METHOD_GET,
            sprintf('/employees/%s/business-trips', $this->employeeId)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('json');
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertStringContainsString('businessTrips', $content);
        $this->assertStringContainsString('_links', $content);
        $data = json_decode($content, true);
        $this->assertCount(2, $data['businessTrips']);
        $lastTrip = $data['businessTrips'][0];
        $this->assertEquals('2023-01-15 08:00:00', $lastTrip['startDate']);
        $this->assertEquals('2023-01-20 16:00:00', $lastTrip['endDate']);
        $this->assertEquals('DE', $lastTrip['countryCode']);
        $this->assertEquals(125, $lastTrip['amountDue']);
        $this->assertEquals('PLN', $lastTrip['currency']);
        $previousTrip = $data['businessTrips'][1];
        $this->assertEquals('2023-01-06 08:00:00', $previousTrip['startDate']);
        $this->assertEquals('2023-01-14 16:00:00', $previousTrip['endDate']);
        $this->assertEquals('PL', $previousTrip['countryCode']);
        $this->assertEquals(600, $previousTrip['amountDue']);
        $this->assertEquals('PLN', $previousTrip['currency']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->createClient();
        $this->client->catchExceptions(true);
        $container = $this->getContainer();
        $employeeRepository = $container->get(EmployeeRepository::class);
        $subsistenceAllowanceRepository = $container->get(SubsistenceAllowanceRepository::class);

        $employee = Employee::create();
        $this->employeeId = $employee->id()->__toString();
        $employeeRepository->save($employee);

        $subsistenceAllowanceRepository->save(SubsistenceAllowance::create('pl', 20));
        $subsistenceAllowanceRepository->save(SubsistenceAllowance::create('de', 25, 'PLN'));

        $businessTrip = BusinessTrip::create(
            EmployeeId::fromString($this->employeeId),
            'pl',
            BusinessTripDuration::create(
                CarbonImmutable::parse('2023-01-06 08:00:00'),
                CarbonImmutable::parse('2023-01-14 16:00:00')
            ),
            AllowancePerDay::create(100, 'PLN')
        );
        $businessTripRepository = $container->get(BusinessTripRepository::class);
        $businessTripRepository->save($businessTrip);

        $container->get(EntityManagerInterface::class)->flush();
    }
}
