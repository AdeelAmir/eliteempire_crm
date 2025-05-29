<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('must be representative');
            $table->integer('team_id')->nullable()->comment('must be from teams table');
            $table->string('lead_number');
            $table->string('owner_occupy')->nullable();
            $table->string('occupancy_status')->nullable();
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->string('phone')->nullable();
            $table->string('phone2')->nullable();
            $table->string('martial_status')->nullable();
            $table->string('spouce')->nullable();
            $table->string('language')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('property_classification')->nullable();
            $table->string('property_type')->nullable();
            $table->string('multi_family')->nullable();
            $table->string('construction_type')->nullable();
            $table->string('year_built')->nullable();
            $table->string('building_size')->nullable();
            $table->string('bedroom')->nullable();
            $table->string('bathroom')->nullable();
            $table->string('lot_size')->nullable();
            $table->string('home_feature')->nullable();
            $table->string('num_of_stories')->nullable();
            $table->string('association_fee')->nullable();
            $table->string('reason_to_sale')->nullable();
            $table->string('picture')->nullable();
            $table->string('lead_condition')->nullable();
            $table->string('lead_source')->nullable();
            $table->string('data_source')->nullable();
            $table->string('asking_price')->nullable();
            $table->double('arv')->nullable();
            $table->double('assignment_fee')->nullable();
            $table->double('rehab_cost')->nullable();
            $table->double('rehab_cost')->nullable();
            $table->double('arv_rehab_cost')->nullable();
            $table->double('arv_sales_closingcost')->nullable();
            $table->double('property_total_value')->nullable();
            $table->double('wholesales_closing_cost')->nullable();
            $table->double('all_in_cost')->nullable();
            $table->double('investor_profit')->nullable();
            $table->double('sales_price')->nullable();
            $table->double('maximum_allow_offer')->nullable();
            $table->double('offer_range_low')->nullable();
            $table->double('offer_range_high')->nullable();
            $table->datetime('appointment_time')->nullable();
            $table->integer('split')->nullable();
            $table->string('email')->nullable();
            $table->integer('company')->nullable();
            $table->string('note')->nullable();
            $table->integer('lead_type')->comment('1- Lead');
            $table->integer('lead_status')->default(3)->comment('1- Interested 2- Not Interested 3- Lead In 4- Do Not Call 5- No Answer 7- Offer Not Given 8- Offer Not Accepted 9- Accepted 10- Negotiating with Seller 11- Agreement Sent 12- Agreement Received 13- Send To Investor 14- Negotiation with Investors 15- Sent to Title 16- Send Contract to Investor 17- EMD Received 18- EMD Not Received 21- Closed WON 22- Deal Lost 23- Wrong Number 24- Inspection');
            $table->string('interested_reason')->nullable();
            $table->string('not_interested_reason')->nullable();
            $table->integer('is_duplicated')->comment('1. Yes, 0. No')->default(0);
            $table->date('lead_date')->nullable();
            $table->integer('confirmed_by')->nullable();
            $table->integer('confirmation_times')->default(0);
            $table->double('contract_amount')->nullable();
            $table->string('investors')->nullable();
            $table->double('emd_amount')->nullable();
            $table->integer('closing_days')->nullable();
            $table->date('close_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
