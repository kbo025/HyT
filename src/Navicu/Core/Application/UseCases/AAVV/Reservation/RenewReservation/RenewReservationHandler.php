<?php
namespace Navicu\Core\Application\UseCases\AAVV\Reservation\RenewReservation;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\LockedAvailabilityService;

class RenewReservationHandler implements Handler
{
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
        LockedAvailabilityService::renewAvailability($command->get('time'),$rf);
        return new ResponseCommandBus(200,'ok');
	}
}