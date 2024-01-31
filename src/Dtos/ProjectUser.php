<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

class ProjectUser extends AbstractDto
{
    public User $user;
    public ?string $token;
}
