<?php
class Phone
{
    public int    $id;
    public int    $contactId;
    public string $number;

    public function __construct(array $data)
    {
        $this->id        = (int)$data['id'];
        $this->contactId = (int)$data['contact_id'];
        $this->number    = $data['phone_number'];
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'contact_id'   => $this->contactId,
            'phone_number' => $this->number,
        ];
    }
}
