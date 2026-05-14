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

namespace BaksDev\Products\Category\Repository\ProjectProfileLandings;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\Project\CategoryProductProject;
use BaksDev\Products\Category\Entity\Project\Landing\CategoryProductProjectLanding;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use Generator;
use InvalidArgumentException;

final class ProjectProfileLandingsRepository implements ProjectProfileLandingsInterface
{

    private CategoryProductUid|false $category;

    public function byCategory(CategoryProductUid $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}


    public function findAll(): Generator|false
    {

        if(false === $this->category)
        {
            throw new InvalidArgumentException(
                sprintf('Не задан параметр category (%s)', self::class.':'.__LINE__)
            );
        }

        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        /* Задать категорию */
        $dbal
            ->from(CategoryProductProject::class, 'category_project')
            ->where('category_project.category = :category')
            ->setParameter(
                key: 'category',
                value: $this->category,
                type: CategoryProductUid::TYPE,
            );


        $dbal
            ->addSelect('category_project_landing.local AS local')
            ->addSelect('category_project_landing.device AS device')
            ->addSelect('category_project_landing.header AS header')
            ->addSelect('category_project_landing.bottom AS bottom')
            ->join(
                'category_project',
                CategoryProductProjectLanding::class,
                'category_project_landing',
                'category_project.id = category_project_landing.project'
            );


        /* Задать профиль - PROJECT_PROFILE */
        if(true === $dbal->isProjectProfile())
        {
            $dbal->andWhere('category_project.profile = :'.$dbal::PROJECT_PROFILE_KEY.' OR category_project.profile IS NULL');
        }


        $result = $dbal->fetchAllHydrate(ProjectProfileLandingsResult::class);

        return ($result->valid() === true) ? $result : false;

    }
}