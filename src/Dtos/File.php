<?php

namespace Pdfsystems\WebDistributionSdk\Dtos;

use Spatie\DataTransferObject\Attributes\MapFrom;

class File extends AbstractDto
{
    public ?int $id;
    public ?string $name;
    public ?string $description;
    public ?string $type;
    public ?string $filename;
    public ?string $extension;
    public ?int $size;
    public ?string $md5;
    public ?string $auth_key;
    public bool $hidden = false;
    public bool $uploaded = false;
    public bool $standard = false;
    public bool $user_uploaded = false;
    #[MapFrom('links.view')]
    public ?string $view_url;
    #[MapFrom('links.download')]
    public ?string $download_url;
}
