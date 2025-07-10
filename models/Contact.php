<?php
class Contact
{
    public int    $id;
    public string $firstName;
    public string $lastName;
    public string $email;
    public string $createdAt;
    /** @var Phone[] */
    public array  $phones = [];

    public function __construct(array $data)
    {
        $this->id         = (int)$data['id'];
        $this->firstName  = $data['first_name'];
        $this->lastName   = $data['last_name'];
        $this->email      = $data['email'];
        $this->createdAt  = $data['created_at'];
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'first_name' => $this->firstName,
            'last_name'  => $this->lastName,
            'email'      => $this->email,
            'created_at' => $this->createdAt,
            'phones'     => array_map(fn($p) => $p->toArray(), $this->phones),
        ];
    }
}
