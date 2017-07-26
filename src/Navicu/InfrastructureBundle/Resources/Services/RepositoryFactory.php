<?php

namespace Navicu\InfrastructureBundle\Resources\Services;

use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
*	Clase RepositoryFactory modela un servicio usado por el commandBus y los handler para obtener
*	determinados repositorios en el momento que sean requeridos
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho
*	@version 19/05/2015
*/
class RepositoryFactory implements RepositoryFactoryInterface
{
	protected $em;

	protected $map = array(
        'CancellationPolicy' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbCancellationPolicyRepository',
            'entity'=>'NavicuDomain:CancellationPolicy'
        ),
        'Category' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbCategoryRepository',
            'entity'=>'Navicu\Core\Domain\Model\Entity\Category'
        ),
        'CancellationPolicyRule' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbCancellationPolicyRuleRepository',
            'entity'=>'NavicuDomain:CancellationPolicyRule'
        ),
        'DailyPack' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbDailyPackRepository',
            'entity'=>'NavicuDomain:DailyPack'
        ),
        'DailyRoom' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbDailyRoomRepository',
            'entity'=>'NavicuDomain:DailyRoom'
        ),
        'Document' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbDocumentRepository',
            'entity'=>'NavicuDomain:Document'
        ),
        'PackLinkage' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbPackLinkageRepository',
            'entity'=>'NavicuDomain:PackLinkage'
        ),
        'Pack' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbPackRepository',
            'entity'=>'NavicuDomain:Pack'
        ),
        'PropertyCancellationPolicy' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbPropertyCancellationPolicyRepository',
            'entity'=>'NavicuDomain:PropertyCancellationPolicy'
        ),
        'Property' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbPropertyRepository',
            'entity'=>'NavicuDomain:Property'
        ),
        'PropertyService' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbPropertyServiceRepository',
            'entity'=>'NavicuDomain:PropertyService'
        ),
        'RateByPeople' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRateByPeopleRepository',
            'entity'=>'NavicuDomain:RateByPeople'
        ),
        'RoomLinkage' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRoomLinkageRepository',
            'entity'=>'NavicuDomain:RoomLinkage'
        ),
        'RoomPackLinkage' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRoomPackLinkageRepository',
            'entity'=>'NavicuDomain:RoomPackLinkage'
        ),
        'Room' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRoomRepository',
            'entity'=>'NavicuDomain:Room'
        ),
        'TempOwner' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbTempOwnerRepository',
            'entity'=>'NavicuDomain:TempOwner'
        ),
        'Location' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbLocationRepository',
            'entity'=>'NavicuDomain:Location'
        ),
        'ServiceType' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbServiceTypeRepository',
            'entity'=>'NavicuDomain:ServiceType'
        ),
        'FoodType' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbFoodTypeRepository',
            'entity'=>'NavicuDomain:FoodType'
        ),
        'RoomType' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRoomTypeRepository',
            'entity'=>'NavicuDomain:RoomType'
        ),
        'RoomFeatureType' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRoomFeatureTypeRepository',
            'entity'=>'NavicuDomain:RoomFeatureType'
        ),
        'Role' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRoleRepository',
            'entity'=>'Navicu\InfrastructureBundle\Entity\Role'
        ),
        'Language' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbLanguageRepository',
            'entity'=>'NavicuDomain:Language'
        ),
        'Accommodation' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbAccommodationRepository',
            'entity'=>'NavicuDomain:Accommodation'
        ),
        'ContactPerson' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbContactPersonRepository',
            'entity'=>'NavicuDomain:ContactPerson'
        ),
        'CurrencyType' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbCurrencyTypeRepository',
            'entity'=>'NavicuDomain:CurrencyType'
        ),
        'Agreement' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbAgreementRepository',
            'entity'=>'NavicuDomain:Agreement'
        ),
        'PaymentInfoProperty' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbPaymentInfoPropertyRepository',
            'entity'=>'NavicuDomain:PaymentInfoProperty'
        ),
        'OwnerProfile' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbOwnerProfileRepository',
            'entity'=>'NavicuDomain:OwnerProfile'
        ),
        'Reservation' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbReservationRepository',
            'entity'=>'NavicuDomain:Reservation'
        ),
        'ReservationPack' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbReservationPackRepository',
            'entity'=>'NavicuDomain:ReservationPack'
        ),
        'ClientProfile' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbClientProfileRepository',
            'entity'=>'NavicuDomain:ClientProfile'
        ),
        'Bed' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbBedRepository',
            'entity'=>'NavicuDomain:Bed'
        ),
        'Bedroom' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbBedroomRepository',
            'entity'=>'NavicuDomain:Bedroom'
        ),
		'SphinxQL' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\SESphinxQLRepository'
		),
        'Restaurant' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRestaurantRepository',
            'entity'=>'NavicuDomain:Restaurant'
        ),
        'Bar' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbBarRepository',
            'entity'=>'NavicuDomain:Bar'
        ),
        'Salon' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbSalonRepository',
            'entity'=>'NavicuDomain:Salon'
        ),
        'LogsOwner'=>array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbLogsOwnerRepository',
            'entity'=>'NavicuDomain:LogsOwner'
        ),
        'LogsUser'=>array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbLogsUserRepository',
            'entity'=>'NavicuDomain:LogsUser'
        ),
        'User' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbUserRepository',
            'entity'=>'Navicu\InfrastructureBundle\Entity\User'
        ),
        'Role' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbRoleRepository',
            'entity'=>'Navicu\InfrastructureBundle\Entity\Role'
        ),
        'BankType' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbBankTypeRepository',
            'entity'=>'NavicuDomain:BankType'
        ),
        'AAVVProfile' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbAAVVProfileRepository',
            'entity'=>'NavicuDomain:AAVVProfile'
        ),
        'DeniedReservation' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbDeniedReservationRepository',
            'entity'=>'NavicuDomain:DeniedReservation'
        ),
        'ExchangeRateHistory' => array(
            'repository'=>'Navicu\InfrastructureBundle\Repositories\DbExchangeRateHistoryRepository',
            'entity'=>'NavicuDomain:ExchangeRateHistory'
        ),
        'AAVVReservationGroupRepository' => array(
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVReservationGroupRepository',
            'entity' => 'NavicuDomain:AAVVReservationGroup'
        ),
        'AAVVReservationGroup' => array(
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAAVVReservationGroupRepository',
            'entity' => 'NavicuDomain:AAVVReservationGroup'
        ),
        'Airport' => array(
            'repository' => 'Navicu\InfrastructureBundle\Repositories\DbAirportRepository',
            'entity' => 'NavicuDomain:Airport'
        ),
    );

	public function __construct(EntityManager $em = null)
	{
		$this->em = $em;
	}

	/**
	*	Devuelve el objeto repoisitorio de la entidad que se pasa por parametro
	*	@return TempOwnerRepository
	*	@author Currently Working: Gabriel Camacho
	*	@version 19/05/2015
	*	@param String
	*	@return EntityRepository
	*/
	public function get($entity)
	{
        $class = $this->map[$entity]['repository'];

		if(array_key_exists($entity, $this->map)) {
            if(isset($this->map[$entity]['entity'])) {
                return new $class($this->em, new ClassMetadata($this->map[$entity]['entity']));
			} else {
                return new $class($this->em);
			}
		}

        return null;
	}
}