<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\ChangeValueMailEncryptionSeeder;

class ChangeTheValueOfMailEncryptionInSmtpAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        (new ChangeValueMailEncryptionSeeder())->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
