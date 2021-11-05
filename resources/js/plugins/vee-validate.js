import { isAfter, isValid, parse } from 'date-fns'
import { configure, extend, localize, ValidationObserver, ValidationProvider } from 'vee-validate'
// setInteractionMode('eager');
import sv from 'vee-validate/dist/locale/sv.json'
import { email, min, required } from 'vee-validate/dist/rules'
import Vue from 'vue'

configure({
    classes: {
        valid: 'is-valid',
        invalid: 'is-invalid',
        dirty: ['is-dirty'] // multiple classes per flag!
    }
})
localize({
    sv: {
        names: {
            first_name: 'förnamn',
            last_name: 'efternamn',
            ssn: 'personnummer',
            name: 'namn',
            email: 'e-post',
            contact_person: 'kontaktperson',
            org_number: 'organisationsnummer',
            project_name: 'projektnamn',
            project_number: 'projektnummer',
            valid_until: 'Giltighetstid',
            company_name: 'företagsnamn',
            billing_post_code: 'postnummer',
            billing_city: 'stad',
            billing_country: 'land',
            password: 'lösenord',
            invite_code: 'inbjudningskod',
            message: 'meddelande',
            contact_email: 'kontaktmail',
            chosenClient: 'beställare',
            header: 'rubrik',
            subheader: 'underrubrik',
            url: 'URL',
            title: 'titel',
            template_id: 'sidtyp'
        }
    }

})
localize({
    sv
})
localize('sv')
extend('email', email)
extend('min', min)
extend('required', required)

extend('url', {
    validate (value, { other }) {
        if (value) {
            return /^(https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*))?$/.test(value)
        }

        return false
    },
    message: 'Angiven URL är inte giltig'
})
extend('after', {
    validate (value, { other }) {
        if (!isValid(value)) {
            return false
        }
        return isAfter(value, other)
    },
    message (fieldName) {
        return 'Datumet är bakåt i tiden'
    },
    castValue: value => parse(value, 'yyyy-MM-dd', new Date()),
    immediate: true,
    params: [
        {
            name: 'other',
            isTarget: true,
            cast (targetValue) {
                return new Date()
            }
        }
    ]
})

// Register it globally
Vue.component('ValidationProvider', ValidationProvider)
Vue.component('ValidationObserver', ValidationObserver)
Vue.component('VeeValidate')
