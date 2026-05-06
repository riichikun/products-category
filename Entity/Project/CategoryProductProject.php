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

namespace BaksDev\Products\Category\Entity\Project;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Products\Category\Entity\Project\Landing\CategoryProductProjectLanding;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\Type\Project\CategoryProductProjectUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;


/* CategoryProductProject */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_project')]
class CategoryProductProject extends EntityState
{
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryProductProjectUid::TYPE)]
    private readonly CategoryProductProjectUid $id;


    #[ORM\Column(type: UserProfileUid::TYPE, nullable: true)]
    private ?UserProfileUid $profile = null;


    /** ID Category */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[Assert\Type(CategoryProductUid::class)]
    #[ORM\Column(type: CategoryProductUid::TYPE, nullable: false)]
    private CategoryProductUid $category;


    /** Посадочные блоки */
    #[ORM\OneToMany(targetEntity: CategoryProductProjectLanding::class, mappedBy: 'project', cascade: ['all'])]
    private Collection $landing;


    public function __construct()
    {
        $this->id = new CategoryProductProjectUid();
    }


    public function __toString(): string
    {
        return (string) $this->id;
    }


    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryProductProjectInterface)
        {

            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {

        if($dto instanceof CategoryProductProjectInterface || $dto instanceof self)
        {

            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}