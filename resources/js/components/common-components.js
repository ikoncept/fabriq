import Vue from 'vue'
import FButton from '~/components/forms/FButton'
import FButtonItem from '~/components/forms/FButtonItem'
import FButtonList from '~/components/forms/FButtonList'
import FButtonSwitch from '~/components/forms/FButtonSwitch'
import FCommentEditor from '~/components/forms/FCommentEditor'
import FConfirm from '~/components/forms/FConfirm'
import FConfirmDropdown from '~/components/forms/FConfirmDropdown'
import FDatePicker from '~/components/forms/FDatePicker'
import FEditor from '~/components/forms/FEditor'
import FFileInput from '~/components/forms/FFileInput'
import FImageInput from '~/components/forms/FImageInput'
import FInput from '~/components/forms/FInput'
import FLabel from '~/components/forms/FLabel'
import FLocaleSelect from '~/components/forms/FLocaleSelect'
import FMediaPicker from '~/components/forms/FMediaPicker'
import FModal from '~/components/forms/FModal'
import FSearchInput from '~/components/forms/FSearchInput'
import FSelect from '~/components/forms/FSelect'
import FSwitch from '~/components/forms/FSwitch'
import FUpload from '~/components/forms/FUpload'
import FVideoInput from '~/components/forms/FVideoInput'
import FTab from '~/components/forms/tabs/FTab'
import FTabs from '~/components/forms/tabs/FTabs'
import UiLogo from '~/components/Logo'
import CreateModal from '~/components/modals/CreateModal'
import FTable from '~/components/table/FTable'
import PresenceInfo from '~/components/ui/PresenceInfo'
import UiAvatar from '~/components/ui/UiAvatar'
import UiBadge from '~/components/ui/UiBadge'
import UiCard from '~/components/ui/UiCard'
import UiDashedBox from '~/components/ui/UiDashedBox'
import UiDropdown from '~/components/ui/UiDropdown'
import UiImagePresenter from '~/components/ui/UiImagePresenter'
import UiSectionHeader from '~/components/ui/UiSectionHeader'
import UiStatsCard from '~/components/ui/UiStatsCard'

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
    PresenceInfo
].forEach(Component => {
    Vue.component(Component.name, Component)
})
