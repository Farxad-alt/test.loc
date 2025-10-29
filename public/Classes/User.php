<?php
class User
{
    public $user_id;
    public $first_name;
    public $last_name;
    public $phone;

    public function __construct($data)
    {
        $this->user_id    = $data['user_id'] ?? null;
        $this->first_name = $data['first_name'] ?? '';
        $this->last_name  = $data['last_name'] ?? '';
        $this->phone      = $data['phone'] ?? '';
    }
}
