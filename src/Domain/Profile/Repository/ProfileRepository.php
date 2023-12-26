<?php

namespace App\Domain\Profile\Repository;

use App\Domain\Profile\entities\Profile;

interface  ProfileRepository
{

    public function createUserProfile(int $userIdx, Profile $profile);
}