<?php

use App\Client\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
class ClientSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $faker = Faker\Factory::create();
    $count = 1;
    $packages = ['Classic', 'Imperia', 'Royale', 'Classic India'];
    $packageType = ['FCLP', 'FCV'];
    foreach (range(1, 5000) as $index) {
      $client = new Client;
      $client->name = $faker->name;
      $client->phone = $faker->phoneNumber;
      $client->email = $faker->email;
      $client->birthDate = $faker->date();
      $client->altPhone = $faker->phoneNumber;
      $client->address = $faker->address;
      $client->save();
      $package = $client->Packages()->create([
        'mafNo' => $count,
        'fclpId' => 'FCLP' . $faker->uuid,
        'branch' => $faker->city,
        'saleBy' => rand(2,103),
        'enrollmentDate' => $faker->date,
        'productType' => Arr::random($packageType,1)[0],
        'productTenure' => $faker->numberBetween(1,4),
        'productName' => Arr::random($packages,1)[0],
        'productCost' => $faker->numberBetween(90000,500000)
//        'modeOfPayment'=>$request->productModeOfPayment,
      ]);
      $client->TimelineActivity()->create([
        'user_id'=>1,
        'title'=>'New Package Purchased',
        'body' => 'Purchased ' . $package->productType . ' ' .$package->productName . ' with a tenure of ' . $package->productTenure .' Years'
      ]);
      $count ++;
    }

  }
}
