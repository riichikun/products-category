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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Tests;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Repository\CategoryProductCurrentEvent\CategoryProductCurrentEventInterface;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductHandler;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\CategoryProductOffersDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Trans\CategoryProductOffersTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\CategoryProductVariationDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification\CategoryProductModificationDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\Modification\Trans\CategoryProductModificationTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation\Trans\CategoryProductVariationTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\CategoryProductSectionCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Fields\CategoryProductSectionFieldCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Fields\Trans\CategoryProductSectionFieldTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Trans\CategoryProductSectionTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Seo\CategoryProductSeoCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Trans\CategoryProductTransDTO;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[Group('products-category')]
#[Group('products-category-usecase')]
#[When(env: 'test')]
class CategoryProductEditTest extends KernelTestCase
{
    #[DependsOnClass(CategoryProductNewTest::class)]
    public function testUseCase(): void
    {
        /** @var CategoryProductCurrentEventInterface $categoryProductCurrentEvent */
        $categoryProductCurrentEvent = self::getContainer()->get(CategoryProductCurrentEventInterface::class);
        $categoryProductEvent = $categoryProductCurrentEvent
            ->forMain(CategoryProductUid::TEST)
            ->find();


        self::assertNotNull($categoryProductEvent);
        self::assertNotFalse($categoryProductEvent);

        /** @see CategoryProductDTO */
        $categoryProductDTO = new CategoryProductDTO();
        $categoryProductEvent->getDto($categoryProductDTO);

        self::assertEquals(123, $categoryProductDTO->getSort());
        $categoryProductDTO->setSort(321);

        $productInfoDTO = $categoryProductDTO->getInfo();


        self::assertFalse($productInfoDTO->getActive());
        $productInfoDTO->setActive(true);


        self::assertEquals('test_category_url', $productInfoDTO->getUrl());
        $productInfoDTO->setUrl('edit_test_category_url');


        //        $categoryProductDTO->getLanding();
        //
        //        /** @var CategoryProductLandingCollectionDTO $productLandingCollectionDTO */
        //        foreach($categoryProductDTO->getLanding() as $productLandingCollectionDTO)
        //        {
        //            self::assertEquals('Test Landing Header', $productLandingCollectionDTO->getHeader());
        //            $productLandingCollectionDTO->setHeader('Edit Test Landing Header');
        //
        //            self::assertEquals('Test Landing Bottom', $productLandingCollectionDTO->getBottom());
        //            $productLandingCollectionDTO->setBottom('Edit Test Landing Bottom');
        //
        //        }


        /** @var CategoryProductSeoCollectionDTO $productSeoCollectionDTO */
        foreach($categoryProductDTO->getSeo() as $productSeoCollectionDTO)
        {
            self::assertEquals('Test Category Seo Title', $productSeoCollectionDTO->getTitle());
            $productSeoCollectionDTO->setTitle('Edit Test Category Seo Title');

            self::assertEquals('Test Category Seo Description', $productSeoCollectionDTO->getDescription());
            $productSeoCollectionDTO->setDescription('Edit Test Category Seo Description');

            self::assertEquals('Test Category Seo Keywords', $productSeoCollectionDTO->getKeywords());
            $productSeoCollectionDTO->setKeywords('Edit Test Category Seo Keywords');

        }


        /** @var CategoryProductSectionCollectionDTO $productSectionCollectionDTO */

        self::assertCount(1, $categoryProductDTO->getSection());
        $productSectionCollectionDTO = $categoryProductDTO->getSection()->current();

        /** @var CategoryProductSectionFieldCollectionDTO $productSectionFieldCollectionDTO */

        self::assertCount(1, $productSectionCollectionDTO->getField());
        $productSectionFieldCollectionDTO = $productSectionCollectionDTO->getField()->current();

        self::assertEquals(112, $productSectionFieldCollectionDTO->getSort());
        $productSectionFieldCollectionDTO->setSort(211);

        self::assertTrue($productSectionFieldCollectionDTO->getType()->getType() === 'input');


        self::assertFalse($productSectionFieldCollectionDTO->getName());
        $productSectionFieldCollectionDTO->setName(true);


        self::assertFalse($productSectionFieldCollectionDTO->getRequired());
        $productSectionFieldCollectionDTO->setRequired(true);


        self::assertFalse($productSectionFieldCollectionDTO->getAlternative());
        $productSectionFieldCollectionDTO->setAlternative(true);

        self::assertFalse($productSectionFieldCollectionDTO->getFilter());
        $productSectionFieldCollectionDTO->setFilter(true);


        self::assertFalse($productSectionFieldCollectionDTO->getPhoto());
        $productSectionFieldCollectionDTO->setPhoto(true);


        self::assertFalse($productSectionFieldCollectionDTO->getPublic());
        $productSectionFieldCollectionDTO->setPublic(true);


        /** @var CategoryProductSectionFieldTransDTO $ProductSectionFieldTransDTO */
        foreach($productSectionFieldCollectionDTO->getTranslate() as $ProductSectionFieldTransDTO)
        {
            self::assertEquals('Test Category Section Field Name', $ProductSectionFieldTransDTO->getName());
            $ProductSectionFieldTransDTO->setName('Edit Test Category Section Field Name');

            self::assertEquals('Test Category Section Field Description', $ProductSectionFieldTransDTO->getDescription());
            $ProductSectionFieldTransDTO->setDescription('Edit Category Section Field Description');

        }


        /** @var CategoryProductSectionTransDTO $ProductSectionTransDTO */
        foreach($productSectionCollectionDTO->getTranslate() as $ProductSectionTransDTO)
        {
            self::assertEquals('Test Category Section Name', $ProductSectionTransDTO->getName());
            $ProductSectionTransDTO->setName('Edit Test Category Section Name');

            self::assertEquals('Test Category Section Description', $ProductSectionTransDTO->getDescription());
            $ProductSectionTransDTO->setDescription('Edit Test Category Section Description');

        }


        /** @var CategoryProductTransDTO $CategoryProductTransDTO */
        foreach($categoryProductDTO->getTranslate() as $CategoryProductTransDTO)
        {
            self::assertEquals('Test Category Name', $CategoryProductTransDTO->getName());
            $CategoryProductTransDTO->setName('Edit Test Category Name');

            self::assertEquals('Test Category Description', $CategoryProductTransDTO->getDescription());
            $CategoryProductTransDTO->setDescription('Edit Test Category Description');

        }


        /** @var CategoryProductOffersDTO $CategoryProductOffersDTO */
        $CategoryProductOffersDTO = $categoryProductDTO->getOffer();

        /** @var CategoryProductOffersTransDTO $ProductOffersTransDTO */
        foreach($CategoryProductOffersDTO->getTranslate() as $ProductOffersTransDTO)
        {
            self::assertEquals('Test Category Offer Name', $ProductOffersTransDTO->getName());
            $ProductOffersTransDTO->setName('Edit Test Category Offer Name');

            self::assertEquals('Test Category Offer Postfix', $ProductOffersTransDTO->getPostfix());
            $ProductOffersTransDTO->setPostfix('Edit Test Category Offer Postfix');

        }

        self::assertTrue($CategoryProductOffersDTO->isOffer());

        self::assertTrue($CategoryProductOffersDTO->getPrice());
        $CategoryProductOffersDTO->setPrice(false);

        self::assertTrue($CategoryProductOffersDTO->getImage());
        $CategoryProductOffersDTO->setImage(true);

        self::assertFalse($CategoryProductOffersDTO->isPostfix());
        $CategoryProductOffersDTO->setPostfix(true);

        self::assertTrue($CategoryProductOffersDTO->getQuantitative());
        $CategoryProductOffersDTO->setQuantitative(true);

        self::assertTrue($CategoryProductOffersDTO->getReference()->getType() === 'input');


        /* * */


        /** @var CategoryProductVariationDTO $CategoryProductVariationDTO */
        $CategoryProductVariationDTO = $CategoryProductOffersDTO->getVariation();

        /** @var CategoryProductVariationTransDTO $CategoryProductVariationTransDTO */
        foreach($CategoryProductVariationDTO->getTranslate() as $CategoryProductVariationTransDTO)
        {
            self::assertEquals('Test Category Variation Name', $CategoryProductVariationTransDTO->getName());
            $CategoryProductVariationTransDTO->setName('Edit Test Category Variation Name');

            self::assertEquals('Test Category Variation Postfix', $CategoryProductVariationTransDTO->getPostfix());
            $CategoryProductVariationTransDTO->setPostfix('Edit Test Category Variation Postfix');

        }

        self::assertTrue($CategoryProductVariationDTO->isVariation());

        self::assertTrue($CategoryProductVariationDTO->getPrice());
        $CategoryProductVariationDTO->setPrice(true);

        self::assertTrue($CategoryProductVariationDTO->getImage());
        $CategoryProductVariationDTO->setImage(true);

        self::assertFalse($CategoryProductVariationDTO->isPostfix());
        $CategoryProductVariationDTO->setPostfix(true);

        self::assertTrue($CategoryProductVariationDTO->getQuantitative());
        $CategoryProductVariationDTO->setQuantitative(true);

        self::assertTrue($CategoryProductVariationDTO->getReference()->getType() === 'input');


        /** @var CategoryProductModificationDTO $CategoryProductModificationDTO */
        $CategoryProductModificationDTO = $CategoryProductVariationDTO->getModification();

        /** @var CategoryProductModificationTransDTO $CategoryProductModificationTransDTO */
        foreach($CategoryProductModificationDTO->getTranslate() as $CategoryProductModificationTransDTO)
        {
            self::assertEquals('Test Category Modification Name', $CategoryProductModificationTransDTO->getName());
            $CategoryProductModificationTransDTO->setName('Edit Test Category Modification Name');

            self::assertEquals('Test Category Modification Postfix', $CategoryProductModificationTransDTO->getPostfix());
            $CategoryProductModificationTransDTO->setPostfix('Edit Test Category Modification Postfix');

        }

        self::assertTrue($CategoryProductModificationDTO->isModification());

        self::assertTrue($CategoryProductModificationDTO->getPrice());
        $CategoryProductModificationDTO->setPrice(true);

        self::assertTrue($CategoryProductModificationDTO->getImage());
        $CategoryProductModificationDTO->setImage(false);

        self::assertTrue($CategoryProductModificationDTO->getPostfix());
        $CategoryProductModificationDTO->setPostfix(false);

        self::assertTrue($CategoryProductModificationDTO->getQuantitative());
        $CategoryProductModificationDTO->setQuantitative(false);

        self::assertTrue($CategoryProductModificationDTO->getReference()->getType() === 'input');


        $categoryProductCurrencyDTO = $categoryProductDTO->getCurrency();
        $categoryProductCurrencyDTO
            ->setOpt(20)
            ->setPrice(30);


        /** @var CategoryProductHandler $CategoryProductHandler */
        $CategoryProductHandler = self::getContainer()->get(CategoryProductHandler::class);
        $handle = $CategoryProductHandler->handle($categoryProductDTO);

        self::assertTrue($handle, 'Ошибка CategoryProduct');
    }
}
