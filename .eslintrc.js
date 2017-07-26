module.exports = {
    globals: {
        angular: true,
        '$': true,
        Routing: true,
        moment: true,
    },
    extends: [
        'eslint:recommended',
        'airbnb',
    ],
    plugins: [
        'babel',
    ],
    parser: 'babel-eslint',
    parserOptions: {
        ecmaVersion: 7,
        ecmaFeatures: {
            experimentalObjectRestSpread: true,
        }
    },
    env: {
        browser: true,
    },
    rules: {
        'arrow-body-style': 0,
        'prefer-arrow-callback': 0,
        'prefer-template': 0,
        'func-names': 0,
        'no-plusplus': 0,
        'no-console': 0,
        'no-underscore-dangle': 0,
        'comma-dangle': ['error', 'always-multiline'],
        quotes: [2, 'single'],
        indent: ['error', 4],
        semi: ['error', 'always'],
        'no-multiple-empty-lines': ['error', {
            'max': 1,
            'maxEOF': 0,
            'maxBOF': 0,
        }],
        'space-before-function-paren': [2, 'never'],
        'no-param-reassign': [2, {
            props: false,
        }],
        'no-bitwise': 0,
        'no-plusplus': 0,

        'object-shorthand': 0,
        'babel/object-shorthand': 0,
        'babel/func-params-comma-dangle': ['error', 'never'],
        'no-prototype-builtins': 0,
        'no-useless-escape': 0,
    },
}
