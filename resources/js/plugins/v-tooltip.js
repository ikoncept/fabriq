import Vue from 'vue'
import { VTooltip, VPopover, VClosePopover } from 'v-tooltip'
import '../../css/v-tooltip.css'

Vue.directive('tooltip', VTooltip)
Vue.directive('close-popover', VClosePopover)
Vue.component('UiPopover', VPopover)
