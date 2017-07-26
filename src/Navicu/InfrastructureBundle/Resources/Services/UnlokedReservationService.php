<?php
namespace Navicu\InfrastructureBundle\Resources\Services;

class UnlokedReservationService {

    private $redis;

    public function __construct($redis,$rf)
    {
        $this->rf = $rf;
        $this->redis = $redis;
    }

    public function lokedReservation($id,$data)
    {
        $stringData = json_encode($data);
        $this->redis->append($id,$stringData);
        $this->redis->expired(180);
    }

    public function unlokedReservation($id)
    {
        $this->redis->del($id);
    }

    public function existReservation($id)
    {
        $this->redis->exist($id);
    }

    public function getReservation($id)
    {
        $val = $this->redis->get($id);
        $val = json_decode($val,true);
        return $val;
    }
} 