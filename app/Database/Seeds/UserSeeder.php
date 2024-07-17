<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        //menambahkan data pada tabel
        $data = array(
            'name'      => 'Admin',
            'email'     => 'admin@anoa.local',
            'username'  => 'admin',
            'password'  => password_hash('password', PASSWORD_BCRYPT),
        );

        $this->db->table('users')->insert($data);

    }
}
