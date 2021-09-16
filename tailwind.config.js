const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')
module.exports = {
    mode: 'jit',
    purge: [
        'resources/js/**/*.vue',
        'resources/js/**/*.js',
        'resources/views/**/*.blade.php'
    ],
    darkMode: false, // or 'media' or 'class'
    theme: {
        ringColor: {
            gray: colors.trueGray,
            red: colors.red,
            gold: {
                100: '#fdfdfb',
                200: '#f0e8db',
                300: '#e2d3bb',
                400: '#d1ba94',
                500: '#bd9b66',
                600: '#b38e51',
                700: '#a07d46',
                800: '#876a3b',
                900: '#6e5630'
            },
            royal: {
                100: '#f1f9fd',
                200: '#9bd0f3',
                300: '#44a7e9',
                400: '#3C627C',
                500: '#0b3b5b',
                600: '#092F49',
                700: '#072337',
                800: '#041824',
                900: '#020C12'
            }
        },
        extend: {
            colors: {
                goldOld: {
                    100: '#fefbf6',
                    200: '#f7e9cf',
                    300: '#f0d39e',
                    400: '#e6b760',
                    500: '#d89822',
                    600: '#c68c1f',
                    700: '#b07c1c',
                    800: '#966a18',
                    900: '#775413'
                },
                gold: {
                    100: '#FDFDFB',
                    200: '#F3EEE7',
                    300: '#e2d3bb',
                    400: '#d1ba94',
                    500: '#bd9b66',
                    600: '#b38e51',
                    700: '#a07d46',
                    800: '#876a3b',
                    900: '#6e5630'
                },
                royal: {
                    50: '#F3F5F7',
                    100: '#CED8DE',
                    200: '#9DB1BD',
                    300: '#6D899D',
                    400: '#3C627C',
                    500: '#0b3b5b',
                    600: '#092F49',
                    700: '#072337',
                    800: '#041824',
                    900: '#020C12'
                },
                gray: {
                    50: '#F9FAFB',
                    100: '#F3F4F6',
                    200: '#E5E7EB',
                    300: '#D1D5DB',
                    400: '#9CA3AF',
                    500: '#6B7280',
                    600: '#4B5563',
                    700: '#374151',
                    800: '#1F2937',
                    900: '#111827'
                }
            },
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
                serif: [...defaultTheme.fontFamily.sans],
                mono: ['Jetbrains Mono', 'Menlo', 'Monaco', 'Consolas', '"Liberation Mono"', '"Courier New"', 'monospace'],
                pop: ['Press Start 2P']
            },
            animation: {
                fadeIn: 'fadeIn 0.4s ease-out forwards'
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: 0, transform: 'translateY(2rem)' },
                    '100%': { opacity: 1, transform: 'translateY(0rem)' }
                }
            },
            maxHeight: theme => ({
                ...theme('spacing')
            }),
            minHeight: theme => ({
                ...theme('spacing')
            }),
            minWidth: theme => ({
                ...theme('spacing')
            }),
            maxWidth: theme => ({
                ...theme('spacing'),
                '8xl': '90rem',
                '10xl': '100rem'
            })
        }
    },
    variants: {
        extend: {

        }
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio')
        // require('@tailwindcss/forms'),
    ]
}
