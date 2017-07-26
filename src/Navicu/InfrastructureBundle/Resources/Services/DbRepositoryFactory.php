<?php
namespace Navicu\InfrastructureBundle\Resources\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

class DbRepositoryFactory implements RepositoryFactoryInterface
{
    private $em;

    private $map = [
        // DbRepository
        'Accommodation' => [
            'entity' => 'NavicuDomain:Accommodation',
            'repository' => 'Navicu\InfrastructureBundle\Repositories\Db\DbAccommodationRepository'
        ],

        // Repository
        'CancellationPolicy' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbCancellationPolicyRepository',
            'entity' => 'NavicuDomain:CancellationPolicy'
        ],
        'Payment' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbPaymentRepository',
            'entity' => 'NavicuDomain:Payment'
        ],
        'CancellationPolicyRule' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbCancellationPolicyRuleRepository',
            'entity' => 'NavicuDomain:CancellationPolicyRule'
        ],
        'Category' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbCategoryRepository',
            'entity' => 'Navicu\Core\Domain\Model\Entity\Category'
        ],
        'DailyPack' => [
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbDailyPackRepository',
            'entity'=>'NavicuDomain:DailyPack'
        ],
        'DailyRoom' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbDailyRoomRepository',
            'entity' => 'NavicuDomain:DailyRoom'
        ],
        'Document' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbDocumentRepository',
            'entity' => 'NavicuDomain:Document'
        ],
        'IpCollection' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbIpCollectionRepository',
            'entity' => 'NavicuDomain:IpCollection'
        ],
        'Notification' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbNotificationRepository',
            'entity' => 'NavicuDomain:Notification'
        ],
        'PackLinkage' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbPackLinkageRepository',
            'entity' => 'NavicuDomain:PackLinkage'
        ],
        'Pack' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbPackRepository',
            'entity' => 'NavicuDomain:Pack'
        ],
        'PropertyCancellationPolicy' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbPropertyCancellationPolicyRepository',
            'entity' => 'NavicuDomain:PropertyCancellationPolicy'
        ],
        'Property' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbPropertyRepository',
            'entity' => 'NavicuDomain:Property'
        ],
        'PropertyService' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbPropertyServiceRepository',
            'entity' => 'NavicuDomain:PropertyService'
        ],
        'RateByPeople' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbRateByPeopleRepository',
            'entity' => 'NavicuDomain:RateByPeople'
        ],
        'RoomLinkage' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbRoomLinkageRepository',
            'entity' => 'NavicuDomain:RoomLinkage'
        ],
        'RoomPackLinkage' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbRoomPackLinkageRepository',
            'entity' => 'NavicuDomain:RoomPackLinkage'
        ],
        'Room' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbRoomRepository',
            'entity' => 'NavicuDomain:Room'
        ],
        'TempOwner' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbTempOwnerRepository',
            'entity' => 'NavicuDomain:TempOwner'
        ],
        'Location' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbLocationRepository',
            'entity' => 'NavicuDomain:Location'
        ],
        'ServiceType' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbServiceTypeRepository',
            'entity' => 'NavicuDomain:ServiceType'
        ],
        'FoodType' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbFoodTypeRepository',
            'entity' => 'NavicuDomain:FoodType'
        ],
        'RoomType' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbRoomTypeRepository',
            'entity' => 'NavicuDomain:RoomType'
        ],
        'RoomFeatureType' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbRoomFeatureTypeRepository',
            'entity' => 'NavicuDomain:RoomFeatureType'
        ],
        'Language' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbLanguageRepository',
            'entity' => 'NavicuDomain:Language'
        ],
        'ContactPerson' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbContactPersonRepository',
            'entity' => 'NavicuDomain:ContactPerson'
        ],
        'CurrencyType' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbCurrencyTypeRepository',
            'entity' => 'NavicuDomain:CurrencyType'
        ],
        'Agreement' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAgreementRepository',
            'entity' => 'NavicuDomain:Agreement'
        ],
        'PaymentInfoProperty' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbPaymentInfoPropertyRepository',
            'entity' => 'NavicuDomain:PaymentInfoProperty'
        ],
        'OwnerProfile' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbOwnerProfileRepository',
            'entity' => 'NavicuDomain:OwnerProfile'
        ],
        'Reservation' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbReservationRepository',
            'entity' => 'NavicuDomain:Reservation'
        ],
        'ReservationPack' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbReservationPackRepository',
            'entity' => 'NavicuDomain:ReservationPack'
        ],
        'ClientProfile' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbClientProfileRepository',
            'entity' => 'NavicuDomain:ClientProfile'
        ],
        'Bed' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbBedRepository',
            'entity' => 'NavicuDomain:Bed'
        ],
        'Bedroom' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbBedroomRepository',
            'entity' => 'NavicuDomain:Bedroom'
        ],
        'SERepository' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\SearchEngineRepository'
        ],
        'LogsOwner' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbLogsOwnerRepository',
            'entity' => 'NavicuDomain:LogsOwner'
        ],
        'LogsUser' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbLogsUserRepository',
            'entity' => 'NavicuDomain:LogsUser'
        ],
        'User' => [
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbUserRepository',
            'entity'=>'Navicu\InfrastructureBundle\Entity\User'
        ],
        'Role' => [
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRoleRepository',
            'entity'=>'Navicu\InfrastructureBundle\Entity\Role'
        ],
        'ModuleAccess' => [
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbModuleAccessRepository',
            'entity'=>'Navicu\InfrastructureBundle\Entity\ModuleAccess'
        ],
        'Permission' => [
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbPermissionRepository',
            'entity'=>'Navicu\InfrastructureBundle\Entity\Permission'
        ],
        'Bar' => [
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbBarRepository',
            'entity'=>'NavicuDomain:Bar'
        ],
        'Salon' => [
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbSalonRepository',
            'entity'=>'NavicuDomain:Salon'
        ],
        'Restaurant' => [
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRestaurantRepository',
            'entity'=>'NavicuDomain:Restaurant'
        ],
        'DestinationsType' => [
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbDestinationsTypeRepository',
            'entity'=>'NavicuDomain:DestinationsType'
        ],
        'PropertyGallery' => [
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbPropertyGalleryRepository',
            'entity'=>'NavicuDomain:PropertyGallery'
        ],
        'RoomImagesGallery' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbRoomImagesGalleryRepository',
            'entity' => 'NavicuDomain:RoomImagesGallery'
        ],
        'PropertyFavoriteImages' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbPropertyFavoriteImagesRepository',
            'entity' => 'NavicuDomain:PropertyFavoriteImages'
        ],
        'PropertyImagesGallery' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbPropertyImagesGalleryRepository',
            'entity' => 'NavicuDomain:PropertyImagesGallery'
        ],
        'BankType' =>[
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbBankTypeRepository',
            'entity'=>'NavicuDomain:BankType'
        ],
        'RedSocial' => [
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRedSocialRepository',
            'entity'=>'NavicuDomain:RedSocial'
        ],
        'CommercialProfile' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbCommercialProfileRepository',
            'entity' => 'NavicuDomain:CommercialProfile'
        ],
        'Profession' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbProfessionRepository',
            'entity' => 'NavicuDomain:Profession'
        ],
        'Hobbies' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbHobbiesRepository',
            'entity' => 'NavicuDomain:Hobbies'
        ],
        'ExchangeRateHistory' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbExchangeRateHistoryRepository',
            'entity' => 'NavicuDomain:ExchangeRateHistory'
        ],
        'AAVVProfile' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVProfileRepository',
            'entity' => 'NavicuDomain:AAVVProfile'
        ],
        'NvcProfile' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbNvcProfileRepository',
            'entity' => 'NavicuDomain:NvcProfile'
        ],
        'Departament' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbDepartamentRepository',
            'entity' => 'NavicuDomain:Departament'
        ],
        'DeniedReservation' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbDeniedReservationRepository',
            'entity' => 'NavicuDomain:DeniedReservation'
        ],
        'NvcGlobal' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbNvcGlobalRepository',
            'entity' => 'NavicuDomain:NvcGlobal'
        ],
        // Repositorio para la seccion de agencias de viajes
        'AAVV' => [
            'entity' => 'NavicuDomain:AAVV',
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbAAVVRepository'
        ],
        'AAVVAddress' => [
            'entity' => 'NavicuDomain:AAVVAddress',
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVAddressRepository'
        ],
        'AAVVAgreement' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVAgreementRepository',
            'entity' => 'NavicuDomain:AAVVAgreement'
        ],
        'AAVVAdditionalQuota' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVAdditionalQuotaRepository',
            'entity' => 'NavicuDomain:AAVVAdditionalQuota'
        ],
        'AAVVLog' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAavvLogRepository',
            'entity' => 'NavicuDomain:AAVVLog'
        ],
        'AAVVFinancialTransactions' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVFinancialTransactionsRepository',
            'entity' => 'NavicuDomain:AAVVFinancialTransactions'
        ],
        'AAVVDocument' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVDocumentRepository',
            'entity' => 'NavicuDomain:AAVVDocument'
        ],
        'LockedAvailability' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbLockedAvailabilityRepository',
            'entity' => 'NavicuDomain:LockedAvailability'
        ],
        'AAVVReservationGroup' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVReservationGroupRepository',
            'entity' => 'NavicuDomain:AAVVReservationGroup'
        ],
        'AAVVCreditNVC' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVCreditNVCRepository',
            'entity' => 'NavicuDomain:AAVVCreditNVC'
        ],
        'AAVVTopDestination' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVTopDestinationRepository',
            'entity' => 'NavicuDomain:AAVVTopDestination'
        ],
        'Subdomain' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbSubdomainRepository',
            'entity' => 'NavicuInfrastructure:Subdomain'
        ],
        'AAVVInvoice' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVInvoiceRepository',
            'entity' => 'NavicuDomain:AAVVInvoice'
        ],
        'AAVVBankPayments' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVBankPaymentsRepository',
            'entity' => 'NavicuDomain:AAVVBankPayments'
        ],
        'AAVVServiceInvoice' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVServiceInvoiceRepository',
            'entity' => 'NavicuDomain:AAVVServiceInvoice'
        ],
        'AAVVServiceInvoicePayment' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVServiceInvoicePaymentRepository',
            'entity' => 'NavicuDomain:AAVVServiceInvoicePayment'
        ],
        'AAVVServicePaymentAllocation' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVServicePaymentAllocationRepository',
            'entity' => 'NavicuDomain:AAVVServicePaymentAllocation'
        ],
        'AAVVInvoicePayments' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVInvoicePaymentsRepository',
            'entity' => 'NavicuDomain:AAVVInvoicePayments'
        ],
        'AAVVAllocationOfInvoicePayment' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVAllocationOfInvoicePaymentRepository',
            'entity' => 'NavicuDomain:AAVVAllocationOfInvoicePayment'
        ],
        'AAVVGlobal' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVGlobalRepository',
            'entity' => 'NavicuDomain:AAVVGlobal'
        ],
        'NVCSequence' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbNVCSequenceRepository',
            'entity' => 'NavicuDomain:NVCSequence'
        ],
        'AAVVStagingAdditionalQouta' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVStagingAdditionalQoutaRepository',
            'entity' => 'NavicuDomain:AAVVStagingAdditionalQouta'
        ],
        'DropDaily' => [
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbDropDailyRepository',
            'entity' => 'NavicuDomain:DropDaily'
        ],
        'ChildrenAge' => [
	        'repository' => 'Navicu\InfrastructureBundle\Repositories\DbChildrenAgeRepository',
	        'entity' => 'NavicuDomain:ChildrenAge'
        ],
        'Airport' => [
	        'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAirportRepository',
	        'entity' => 'NavicuDomain:Airport'
        ],
        'FlightReservation' => [
	        'repository' => 'Navicu\InfrastructureBundle\Repositories\DbFlightReservationRepository',
	        'entity' => 'NavicuDomain:FlightReservation'
        ],
        'Flight' => [
	        'repository' => 'Navicu\InfrastructureBundle\Repositories\DbFlightRepository',
	        'entity' => 'NavicuDomain:Flight'
        ],
        'Passenger' => [
	        'repository' => 'Navicu\InfrastructureBundle\Repositories\DbChildrenAgeRepository',
	        'entity' => 'NavicuDomain:ChildrenAge'
        ],
        'FlightPayment' => [
	        'repository' => 'Navicu\InfrastructureBundle\Repositories\DbFlightPaymentRepository',
	        'entity' => 'NavicuDomain:FlightPayment'
        ],
    ];

    public function __construct(EntityManager $em = null)
    {
        $this->em = $em;
    }

    /**
     *	Devuelve el objeto repoisitorio de la entidad que se pasa por parametro
     *
     *	@return TempOwnerRepository
     *  @author Gabriel Camacho
     *	@author Currently Working: Juan Pablo Osorio
     *	@version 19/05/2015
     *	@param String
     *	@return DbRepository
     */
    public function get($entity)
    {
        $className = $this->map[$entity]['repository'];

        if (array_key_exists($entity, $this->map)) {
            if (in_array($entity, ['Accommodation']) === true) { // nuevo sistema
                return new $className($this->em, $this->map[$entity]['entity']);
            } else { // viejo sistema
                if (isset($this->map[$entity]['entity'])) {
                    return new $className($this->em, new ClassMetadata($this->map[$entity]['entity']));
                } else {
                    return new $className($this->em);
                }
            }
        }

        return null;
    }
}

/* End of file */
