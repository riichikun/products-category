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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Project\CategoryProductProject;

final class CategoryProductHandler extends AbstractHandler
{
    public function handle(CategoryProductDTO $command): string|bool
    {
        /** Делаем сброс иерархии настроек торговых предложений  */
        $command->resetOffer();

        /** Валидация DTO  */
        $this
            ->setCommand($command)
            ->prePersistOrUpdate(
                entity: CategoryProductEvent::class,
                criteria: ['id' => $command->getEvent()],
                main: CategoryProduct::class,
            );

        /** Загружаем файл обложки раздела */

        if(method_exists($command, 'getCover'))
        {
            $Cover = $command->getCover();

            if($Cover && $Cover->file !== null)
            {
                $ProductCategoryCover = $this->main->getUploadCover();
                $this->imageUpload->upload($Cover->file, $ProductCategoryCover);
            }
        }

        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }


        if(true === ($this->main instanceof CategoryProductEvent))
        {
            /** Сохранение сущности CategoryProductProject */

            /* Подготовить данные по категории и профилю */
            $CategoryProductProjectDTO = $command->getProject();

            $landings = $CategoryProductProjectDTO->getLanding()->filter(
                function($item) {
                    $header = $item->getHeader();
                    $bottom = $item->getBottom();
                    return false === empty($header) && false === empty($bottom);
                },
            );

            /** Сохраняем объект без посадочных блоков */
            if($landings->isEmpty())
            {
                $this->flush();

                $this->messageDispatch
                    ->addClearCacheOther('products-category')
                    ->addClearCacheOther('products-product')
                    ->addClearCacheOther('avito-board');

                return true;
            }


            $CategoryProductProjectDTO->setProfile($command->getProfile());
            $CategoryProductProjectDTO->setCategory($this->main->getCategory());
            $CategoryProductProjectDTO->setLanding($landings);

            /* Проверить на существование CategoryProductProject */

            $ExistingCategoryProject = $this->getRepository(CategoryProductProject::class)
                ->findOneBy([
                    'category' => $this->main->getCategory(),
                    'profile' => $command->getProfile(),
                ]);


            $CategoryProductProject = (true === ($ExistingCategoryProject instanceof CategoryProductProject))
                ? $ExistingCategoryProject
                : new CategoryProductProject();


            /* Если есть сущность CategoryProductProject */
            if(true === ($ExistingCategoryProject instanceof CategoryProductProject))
            {
                $ExistingCategoryProject->setEntityManager($this->getEntityManager());
            }


            $CategoryProductProject->setEntity($CategoryProductProjectDTO);

            /* Создается новая сущность CategoryProductProject */
            if(false === ($ExistingCategoryProject instanceof CategoryProductProject))
            {
                $this->persist($CategoryProductProject);
            }

        }

        $this->flush();

        /* Отправляем событие в шину  */
        $this->messageDispatch
            ->addClearCacheOther('products-category')
            ->addClearCacheOther('products-product')
            ->addClearCacheOther('avito-board');

        return true;
    }
}
