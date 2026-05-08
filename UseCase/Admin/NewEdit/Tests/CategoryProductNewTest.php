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
use BaksDev\Core\Type\Field\InputField;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\CategoryProductHandler;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Currency\CategoryProductCurrencyDTO;
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
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[Group('products-category')]
#[Group('products-category-usecase')]
#[Group('products-product-repository')]
#[When(env: 'test')]
final class CategoryProductNewTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $main = $em->getRepository(CategoryProduct::class)
            ->findOneBy(['id' => CategoryProductUid::TEST]);

        if($main)
        {
            $em->remove($main);
        }


        $event = $em->getRepository(CategoryProductEvent::class)
            ->findBy(['category' => CategoryProductUid::TEST]);

        foreach($event as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();
        $em->clear();
    }


    public function testUseCase(): void
    {
        /** @see CategoryProductDTO */
        $categoryProductDTO = new CategoryProductDTO();


        $categoryProductDTO->setSort(123);
        self::assertEquals('123', $categoryProductDTO->getSort());

        $ProductInfoDTO = $categoryProductDTO->getInfo();


        $ProductInfoDTO->setActive(true);
        self::assertTrue($ProductInfoDTO->getActive());
        $ProductInfoDTO->setActive(false);
        self::assertFalse($ProductInfoDTO->getActive());


        $ProductInfoDTO->setUrl('test_category_url');
        self::assertEquals('test_category_url', $ProductInfoDTO->getUrl());

        // @deprecated
        //        $categoryProductDTO->getLanding();
        //
        //        /** @var CategoryProductLandingCollectionDTO $ProductLandingCollectionDTO */
        //        foreach($categoryProductDTO->getLanding() as $ProductLandingCollectionDTO)
        //        {
        //            $ProductLandingCollectionDTO->setHeader('Test Landing Header');
        //            self::assertEquals('Test Landing Header', $ProductLandingCollectionDTO->getHeader());
        //
        //            $ProductLandingCollectionDTO->setBottom('Test Landing Bottom');
        //            self::assertEquals('Test Landing Bottom', $ProductLandingCollectionDTO->getBottom());
        //        }


        /** @var CategoryProductSeoCollectionDTO $ProductSeoCollectionDTO */
        foreach($categoryProductDTO->getSeo() as $ProductSeoCollectionDTO)
        {
            $ProductSeoCollectionDTO->setTitle('Test Category Seo Title');
            self::assertEquals('Test Category Seo Title', $ProductSeoCollectionDTO->getTitle());

            $ProductSeoCollectionDTO->setDescription('Test Category Seo Description');
            self::assertEquals('Test Category Seo Description', $ProductSeoCollectionDTO->getDescription());

            $ProductSeoCollectionDTO->setKeywords('Test Category Seo Keywords');
            self::assertEquals('Test Category Seo Keywords', $ProductSeoCollectionDTO->getKeywords());

        }


        /** @var CategoryProductSectionCollectionDTO $ProductSectionCollectionDTO */

        $ProductSectionCollectionDTO = new CategoryProductSectionCollectionDTO();
        $categoryProductDTO->addSection($ProductSectionCollectionDTO);
        self::assertCount(1, $categoryProductDTO->getSection());


        $ProductSectionFieldCollectionDTO = new CategoryProductSectionFieldCollectionDTO();

        $ProductSectionFieldCollectionDTO->setSort(112);
        self::assertEquals(112, $ProductSectionFieldCollectionDTO->getSort());

        $ProductSectionFieldCollectionDTO->setType($InputField = new InputField('input'));
        self::assertSame($InputField, $ProductSectionFieldCollectionDTO->getType());

        $ProductSectionFieldCollectionDTO->setName(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getName());
        $ProductSectionFieldCollectionDTO->setName(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getName());

        $ProductSectionFieldCollectionDTO->setRequired(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getRequired());
        $ProductSectionFieldCollectionDTO->setRequired(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getRequired());

        $ProductSectionFieldCollectionDTO->setAlternative(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getAlternative());
        $ProductSectionFieldCollectionDTO->setAlternative(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getAlternative());

        $ProductSectionFieldCollectionDTO->setFilter(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getFilter());
        $ProductSectionFieldCollectionDTO->setFilter(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getFilter());

        $ProductSectionFieldCollectionDTO->setPhoto(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getPhoto());
        $ProductSectionFieldCollectionDTO->setPhoto(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getPhoto());

        $ProductSectionFieldCollectionDTO->setPublic(true);
        self::assertTrue($ProductSectionFieldCollectionDTO->getPublic());
        $ProductSectionFieldCollectionDTO->setPublic(false);
        self::assertFalse($ProductSectionFieldCollectionDTO->getPublic());


        /** @var CategoryProductSectionFieldTransDTO $ProductSectionFieldTransDTO */
        foreach($ProductSectionFieldCollectionDTO->getTranslate() as $ProductSectionFieldTransDTO)
        {
            $ProductSectionFieldTransDTO->setName('Test Category Section Field Name');
            self::assertEquals('Test Category Section Field Name', $ProductSectionFieldTransDTO->getName());
            $ProductSectionFieldTransDTO->setDescription('Test Category Section Field Description');
            self::assertEquals('Test Category Section Field Description', $ProductSectionFieldTransDTO->getDescription());
        }


        $ProductSectionCollectionDTO->addField($ProductSectionFieldCollectionDTO);
        self::assertCount(1, $ProductSectionCollectionDTO->getField());

        /** @var CategoryProductSectionTransDTO $ProductSectionTransDTO */
        foreach($ProductSectionCollectionDTO->getTranslate() as $ProductSectionTransDTO)
        {
            $ProductSectionTransDTO->setName('Test Category Section Name');
            self::assertEquals('Test Category Section Name', $ProductSectionTransDTO->getName());

            $ProductSectionTransDTO->setDescription('Test Category Section Description');
            self::assertEquals('Test Category Section Description', $ProductSectionTransDTO->getDescription());
        }


        /** @var CategoryProductTransDTO $CategoryProductTransDTO */
        foreach($categoryProductDTO->getTranslate() as $CategoryProductTransDTO)
        {
            $CategoryProductTransDTO->setName('Test Category Name');
            self::assertEquals('Test Category Name', $CategoryProductTransDTO->getName());

            $CategoryProductTransDTO->setDescription('Test Category Description');
            self::assertEquals('Test Category Description', $CategoryProductTransDTO->getDescription());
        }


        /** @var CategoryProductOffersDTO $CategoryProductOffersDTO */
        $CategoryProductOffersDTO = $categoryProductDTO->getOffer();

        /** @var CategoryProductOffersTransDTO $ProductOffersTransDTO */
        foreach($CategoryProductOffersDTO->getTranslate() as $ProductOffersTransDTO)
        {
            $ProductOffersTransDTO->setName('Test Category Offer Name');
            self::assertEquals('Test Category Offer Name', $ProductOffersTransDTO->getName());

            $ProductOffersTransDTO->setPostfix('Test Category Offer Postfix');
            self::assertEquals('Test Category Offer Postfix', $ProductOffersTransDTO->getPostfix());
        }

        $CategoryProductOffersDTO->setArticle(false);
        self::assertFalse($CategoryProductOffersDTO->getArticle());
        $CategoryProductOffersDTO->setArticle(true);
        self::assertTrue($CategoryProductOffersDTO->getArticle());

        $CategoryProductOffersDTO->setOffer(false);
        self::assertFalse($CategoryProductOffersDTO->isOffer());
        $CategoryProductOffersDTO->setOffer(true);
        self::assertTrue($CategoryProductOffersDTO->isOffer());


        $CategoryProductOffersDTO->setPrice(false);
        self::assertFalse($CategoryProductOffersDTO->getPrice());
        $CategoryProductOffersDTO->setPrice(true);
        self::assertTrue($CategoryProductOffersDTO->getPrice());


        $CategoryProductOffersDTO->setImage(false);
        self::assertFalse($CategoryProductOffersDTO->getImage());
        $CategoryProductOffersDTO->setImage(true);
        self::assertTrue($CategoryProductOffersDTO->getImage());


        $CategoryProductOffersDTO->setPostfix(false);
        self::assertFalse($CategoryProductOffersDTO->isPostfix());
        $CategoryProductOffersDTO->setPostfix(true);
        self::assertTrue($CategoryProductOffersDTO->isPostfix());


        $CategoryProductOffersDTO->setQuantitative(false);
        self::assertFalse($CategoryProductOffersDTO->getQuantitative());
        $CategoryProductOffersDTO->setQuantitative(true);
        self::assertTrue($CategoryProductOffersDTO->getQuantitative());


        $CategoryProductOffersDTO->setReference($InputField = new InputField('input'));
        self::assertSame($InputField, $CategoryProductOffersDTO->getReference());


        /** @var CategoryProductVariationDTO $CategoryProductVariationDTO */
        $CategoryProductVariationDTO = $CategoryProductOffersDTO->getVariation();

        /** @var CategoryProductVariationTransDTO $CategoryProductVariationTransDTO */
        foreach($CategoryProductVariationDTO->getTranslate() as $CategoryProductVariationTransDTO)
        {
            $CategoryProductVariationTransDTO->setName('Test Category Variation Name');
            self::assertEquals('Test Category Variation Name', $CategoryProductVariationTransDTO->getName());

            $CategoryProductVariationTransDTO->setPostfix('Test Category Variation Postfix');
            self::assertEquals('Test Category Variation Postfix', $CategoryProductVariationTransDTO->getPostfix());
        }


        $CategoryProductVariationDTO->setArticle(false);
        self::assertFalse($CategoryProductVariationDTO->getArticle());
        $CategoryProductVariationDTO->setArticle(true);
        self::assertTrue($CategoryProductVariationDTO->getArticle());


        $CategoryProductVariationDTO->setVariation(false);
        self::assertFalse($CategoryProductVariationDTO->isVariation());
        $CategoryProductVariationDTO->setVariation(true);
        self::assertTrue($CategoryProductVariationDTO->isVariation());


        $CategoryProductVariationDTO->setPrice(false);
        self::assertFalse($CategoryProductVariationDTO->getPrice());
        $CategoryProductVariationDTO->setPrice(true);
        self::assertTrue($CategoryProductVariationDTO->getPrice());


        $CategoryProductVariationDTO->setImage(false);
        self::assertFalse($CategoryProductVariationDTO->getImage());
        $CategoryProductVariationDTO->setImage(true);
        self::assertTrue($CategoryProductVariationDTO->getImage());


        $CategoryProductVariationDTO->setPostfix(false);
        self::assertFalse($CategoryProductVariationDTO->isPostfix());
        $CategoryProductVariationDTO->setPostfix(true);
        self::assertTrue($CategoryProductVariationDTO->isPostfix());


        $CategoryProductVariationDTO->setQuantitative(false);
        self::assertFalse($CategoryProductVariationDTO->getQuantitative());
        $CategoryProductVariationDTO->setQuantitative(true);
        self::assertTrue($CategoryProductVariationDTO->getQuantitative());


        $CategoryProductVariationDTO->setReference($InputField = new InputField('input'));
        self::assertSame($InputField, $CategoryProductVariationDTO->getReference());


        /** @var CategoryProductModificationDTO $CategoryProductModificationDTO */
        $CategoryProductModificationDTO = $CategoryProductVariationDTO->getModification();

        /** @var CategoryProductModificationTransDTO $CategoryProductModificationTransDTO */
        foreach($CategoryProductModificationDTO->getTranslate() as $CategoryProductModificationTransDTO)
        {
            $CategoryProductModificationTransDTO->setName('Test Category Modification Name');
            self::assertEquals('Test Category Modification Name', $CategoryProductModificationTransDTO->getName());

            $CategoryProductModificationTransDTO->setPostfix('Test Category Modification Postfix');
            self::assertEquals('Test Category Modification Postfix', $CategoryProductModificationTransDTO->getPostfix());
        }


        $CategoryProductModificationDTO->setArticle(false);
        self::assertFalse($CategoryProductModificationDTO->getArticle());
        $CategoryProductModificationDTO->setArticle(true);
        self::assertTrue($CategoryProductModificationDTO->getArticle());

        $CategoryProductModificationDTO->setModification(false);
        self::assertFalse($CategoryProductModificationDTO->isModification());
        $CategoryProductModificationDTO->setModification(true);
        self::assertTrue($CategoryProductModificationDTO->isModification());

        $CategoryProductModificationDTO->setPrice(false);
        self::assertFalse($CategoryProductModificationDTO->getPrice());
        $CategoryProductModificationDTO->setPrice(true);
        self::assertTrue($CategoryProductModificationDTO->getPrice());

        $CategoryProductModificationDTO->setImage(false);
        self::assertFalse($CategoryProductModificationDTO->getImage());
        $CategoryProductModificationDTO->setImage(true);
        self::assertTrue($CategoryProductModificationDTO->getImage());


        $CategoryProductModificationDTO->setPostfix(false);
        self::assertFalse($CategoryProductModificationDTO->getPostfix());
        $CategoryProductModificationDTO->setPostfix(true);
        self::assertTrue($CategoryProductModificationDTO->getPostfix());


        $CategoryProductModificationDTO->setQuantitative(false);
        self::assertFalse($CategoryProductModificationDTO->getQuantitative());
        $CategoryProductModificationDTO->setQuantitative(true);
        self::assertTrue($CategoryProductModificationDTO->getQuantitative());


        $CategoryProductModificationDTO->setReference($InputField = new InputField('input'));
        self::assertSame($InputField, $CategoryProductModificationDTO->getReference());


        $categoryProductCurrencyDTO = new CategoryProductCurrencyDTO();
        $categoryProductDTO->setCurrency($categoryProductCurrencyDTO);
        $categoryProductCurrencyDTO
            ->setOpt(10)
            ->setPrice(20);

        /** @var CategoryProductHandler $CategoryProductHandler */
        $CategoryProductHandler = self::getContainer()->get(CategoryProductHandler::class);
        $handle = $CategoryProductHandler->handle($categoryProductDTO);

        self::assertTrue($handle, 'Ошибка CategoryProduct');

    }

    public function testComplete(): void
    {
        /** @var DBALQueryBuilder $dbal */
        $dbal = self::getContainer()->get(DBALQueryBuilder::class);

        $dbal->createQueryBuilder(self::class);

        $dbal->from(CategoryProduct::class)
            ->where('id = :id')
            ->setParameter('id', CategoryProductUid::TEST);

        self::assertTrue($dbal->fetchExist());
    }
}
