<?php

namespace App\Models;

use CodeIgniter\Model;

class Bank_model extends Model
{
    protected $table = 'tbank';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode', 'nama'];

    public function getBanks()
    {
        return $this->orderBy('nama', 'ASC')->findAll();
    }

    public function getBankByKode($kode)
    {
        return $this->where('kode', $kode)->first();
    }

    public function insertBank($nama)
    {
        // Generate kode from first 3 characters of nama
        $kode = strtoupper(substr($nama, 0, 3));
        
        // Check if kode already exists, if so, add number suffix
        $existingBank = $this->where('kode', $kode)->first();
        if ($existingBank) {
            $counter = 1;
            $originalKode = $kode;
            do {
                $kode = $originalKode . $counter;
                $existingBank = $this->where('kode', $kode)->first();
                $counter++;
            } while ($existingBank);
        }

        $data = [
            'kode' => $kode,
            'nama' => $nama
        ];

        $result = $this->insert($data);
        if ($result) {
            return [
                'success' => true,
                'kode' => $kode,
                'id' => $this->getInsertID()
            ];
        }
        
        return ['success' => false];
    }

    public function getBankOptions()
    {
        $banks = $this->getBanks();
        $options = [];
        foreach ($banks as $bank) {
            $options[$bank['kode']] = $bank['nama'];
        }
        return $options;
    }
}
