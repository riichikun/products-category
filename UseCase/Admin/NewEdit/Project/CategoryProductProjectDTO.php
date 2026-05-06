<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Project;

use BaksDev\Core\Type\Device\Device;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Project\CategoryProductProjectInterface;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Project\Landing\CategoryProductProjectLandingDTO;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryProductProjectDTO implements CategoryProductProjectInterface
{
    /* Категория */
    //#[Assert\NotBlank]
    private CategoryProductUid $category;

    /* Профиль */
    private ?UserProfileUid $profile = null;

    /* Посадочные блоки */
    #[Assert\Valid]
    private ArrayCollection $landing;


    public function __construct()
    {
        $this->landing = new ArrayCollection();
    }


    public function getCategory(): CategoryProductUid
    {
        return $this->category;
    }

    public function setCategory(CategoryProductUid $category): self
    {
        $this->category = $category;
        return $this;
    }


    /* Посадочные блоки */

    public function getLanding(): ArrayCollection
    {

        /* Вычислить расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->landing) as $locale)
        {
            /** @var Device $device */
            foreach(Device::cases() as $device)
            {
                $CategoryProductProjectLandingDTO = new CategoryProductProjectLandingDTO();
                $CategoryProductProjectLandingDTO->setLocal($locale);
                $CategoryProductProjectLandingDTO->setDevice($device);

                $this->addLanding($CategoryProductProjectLandingDTO);
            }
        }

        return $this->landing;
    }

    public function setLanding(ArrayCollection $landing): void
    {
        $this->landing = $landing;
    }

    public function addLanding(CategoryProductProjectLandingDTO $landing): void
    {
        if(empty($landing->getLocal()->getLocalValue()))
        {
            return;
        }

        if(!$this->landing->contains($landing))
        {
            $this->landing->add($landing);
        }
    }

    /* Профиль */
    public function getProfile(): ?UserProfileUid
    {
        return $this->profile;
    }

    public function setProfile(?UserProfileUid $profile): self
    {
        $this->profile = $profile;
        return $this;
    }

}