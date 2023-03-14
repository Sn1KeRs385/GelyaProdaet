module.exports = {
  extends: [
    'stylelint-prettier/recommended',
    'stylelint-config-idiomatic-order',
    './node_modules/prettier-stylelint/config.js',
  ],
  plugins: ['stylelint-prettier'],
  rules: {
    'prettier/prettier': true,
  },
}
