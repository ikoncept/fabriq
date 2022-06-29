import FButton from '@/components/forms/FButton.vue'
import FButtonItem from '@/components/forms/FButtonItem.vue'
import FButtonList from '@/components/forms/FButtonList.vue'
import FButtonSwitch from '@/components/forms/FButtonSwitch.vue'
import FCommentEditor from '@/components/forms/FCommentEditor.vue'
import FConfirm from '@/components/forms/FConfirm.vue'
import FConfirmDropdown from '@/components/forms/FConfirmDropdown.vue'
import FDatePicker from '@/components/forms/FDatePicker.vue'
import FEditor from '@/components/forms/FEditor.vue'
import FFileInput from '@/components/forms/FFileInput.vue'
import FImageInput from '@/components/forms/FImageInput.vue'
import FInput from '@/components/forms/FInput.vue'
import FLabel from '@/components/forms/FLabel.vue'
import FLocaleSelect from '@/components/forms/FLocaleSelect.vue'
import FMediaPicker from '@/components/forms/FMediaPicker.vue'
import FModal from '@/components/forms/FModal.vue'
import FSearchInput from '@/components/forms/FSearchInput.vue'
import FSelect from '@/components/forms/FSelect.vue'
import FSwitch from '@/components/forms/FSwitch.vue'
import FUpload from '@/components/forms/FUpload.vue'
import FVideoInput from '@/components/forms/FVideoInput.vue'
import HelpText from '@/components/forms/HelpText.vue'
import FTab from '@/components/forms/tabs/FTab.vue'
import FTabs from '@/components/forms/tabs/FTabs.vue'
import UiLogo from '@/components/Logo.vue'
import CreateModal from '@/components/modals/CreateModal.vue'
import FTable from '@/components/table/FTable.vue'
import PresenceInfo from '@/components/ui/PresenceInfo.vue'
import UiAvatar from '@/components/ui/UiAvatar.vue'
import UiBadge from '@/components/ui/UiBadge.vue'
import UiCard from '@/components/ui/UiCard.vue'
import UiDashedBox from '@/components/ui/UiDashedBox.vue'
import UiDropdown from '@/components/ui/UiDropdown.vue'
import UiImagePresenter from '@/components/ui/UiImagePresenter.vue'
import UiSectionHeader from '@/components/ui/UiSectionHeader.vue'
import UiStatsCard from '@/components/ui/UiStatsCard.vue'
import Vue from 'vue'

[
    UiLogo,
    UiCard,
    UiStatsCard,
    UiBadge,
    FSwitch,
    FInput,
    FLabel,
    FTable,
    FEditor,
    FButton,
    FUpload,
    FLocaleSelect,
    FMediaPicker,
    UiImagePresenter,
    UiSectionHeader,
    FImageInput,
    FModal,
    FConfirm,
    FCommentEditor,
    FSelect,
    UiDropdown,
    FSearchInput,
    FDatePicker,
    FConfirmDropdown,
    FTab,
    FTabs,
    FButtonList,
    FButtonSwitch,
    FButtonItem,
    FFileInput,
    FVideoInput,
    CreateModal,
    UiDashedBox,
    UiAvatar,
    PresenceInfo,
    HelpText
].forEach(Component => {
    Vue.component(Component.name, Component)
})
