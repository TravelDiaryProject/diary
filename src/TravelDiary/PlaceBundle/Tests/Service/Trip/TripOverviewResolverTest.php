<?php

namespace TravelDiary\PlaceBundle\Tests\Service\Trip;

use TravelDiary\PlaceBundle\Service\Trip\TripOverviewDatesResolver;
use TravelDiary\PlaceBundle\Service\Trip\TripOverviewGeoResolver;
use TravelDiary\PlaceBundle\Service\Trip\TripOverviewResolver;
use TravelDiary\TripBundle\Entity\Trip;

/**
 * Class TripOverviewResolverTest
 */
class TripOverviewResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function providerResolve()
    {
        return [
            ['geo', 'dates', 'geo | dates'],
            ['geo', '', 'geo'],
            ['', 'dates', 'dates'],
            ['', '', ''],
        ];
    }

    /**
     * @dataProvider providerResolve
     *
     * @param string $geoResolverResult
     * @param string $datesResolverResult
     * @param string $expectedResult
     */
    public function testResolve($geoResolverResult, $datesResolverResult, $expectedResult)
    {
        $geoResolver = $this->getMockBuilder(TripOverviewGeoResolver::class)
            ->disableOriginalConstructor()->getMock();

        $geoResolver->expects(static::once())
            ->method('resolve')
            ->willReturn($geoResolverResult);

        $datesResolver = $this->getMockBuilder(TripOverviewDatesResolver::class)
            ->disableOriginalConstructor()->getMock();

        $datesResolver->expects(static::once())
            ->method('resolve')
            ->willReturn($datesResolverResult);

        $resolver = new TripOverviewResolver($geoResolver, $datesResolver);

        static::assertEquals($expectedResult, $resolver->resolve(new Trip()));
    }
}