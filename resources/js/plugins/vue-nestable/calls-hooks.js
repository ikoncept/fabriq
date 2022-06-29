export default {
    methods: {
        hook (name, parameters) {
            // If the hook has not been registered,
            // we consider the hook as successful
            if (!this.hooks[name]) return true

            const result = this.hooks[name](parameters)

            // If the hook does not return anything,
            // we also consider it true
            return result || result === undefined
        }
    }
}
