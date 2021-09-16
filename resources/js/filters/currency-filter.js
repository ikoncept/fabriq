import Vue from 'vue'

import VueCurrencyFilter from 'vue-currency-filter'

Vue.use(VueCurrencyFilter,
    {
        symbol: 'kr',
        thousandsSeparator: ' ',
        fractionCount: 2,
        fractionSeparator: ',',
        symbolPosition: 'end',
        symbolSpacing: true,
        avoidEmptyDecimals: ''
    })
