<?php
class Plot
{
    public $plot_id;
    public $status;
    public $billing;
    public $number;
    public $size;
    public $price;
    public $users = [];

    public function __construct($data)
    {
        $this->plot_id = $data['plot_id'] ?? null;
        $this->status  = $data['status'] ?? '';
        $this->billing = $data['billing'] ?? '';
        $this->number  = $data['number'] ?? '';
        $this->size    = $data['size'] ?? 0;
        $this->price   = $data['price'] ?? 0;
    }

    public function addUser(User $user)
    {
        $this->users[] = $user;
    }
}
