import React from 'react';

import ESimTelBanner from './ESimTelBanner';
import EsimToRule from './EsimToRule';
import PopularPlans from './PopulerPlan';
import FAQ from './sections/FAQ';
import Hero from './sections/Hero';
import TravelDestinationTabs from './sections/TravelDestinationTabs';
import WhyChoose from './sections/WhyChoose';

const Main = () => {
    return (
        <div>
            <Hero />
            <EsimToRule />
            <ESimTelBanner />
            <TravelDestinationTabs />
            <PopularPlans />
            <WhyChoose />
            <FAQ />
        </div>
    );
};

export default Main;
