<?php

namespace App\Entities;

use CodeIgniter\Entity;

class ComapnyProfile extends Entity
{
    protected $companyProfileId;
    public $name;
    public $noTelp;
    public $address;
    public $timestamp;

    protected $_options = [
        'datamap' => [
            'name' => 'name'
        ],
        'dates' => ['timestamp']
    ];

    public function getCreatedAt(string $format = 'Y-m-d H:i:s')
    {
        $timezone = isset($this->timezone)
            ? $this->timezone
            : app_timezone();

        $this->timestamp->setTimezone($timezone);

        return $this->timestamp->format($format);
    }

}