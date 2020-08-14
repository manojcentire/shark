describe('Authentication', () => {

    before(() => cy.refreshDatabase());

    describe('guest', function () {

        it('is redirected to login page', () => {
            cy.visit('/')
                .assertRedirect('/auth');
        });

        it('sees the login page with form and required fields', () => {
            cy.visit('/auth')
                .get('form[action$="/auth"]')
                .as('form')
                .should('have.attr', 'method', 'POST')
                .should('have.attr', 'action', Cypress.config('baseUrl') + '/auth');

                cy.get('@form')
                .get('[name="shop"]')
                .as('input.shop')
                .should('have.attr', 'name', 'shop');

                cy.get('@form')
                .get('[type="submit"]')
                .as('input.submit')
                .should('have.attr', 'type', 'submit');
        });

        it('cannot submit login form if shop domain is not filled', () => {
            cy.visit('/auth')
                .get('form[action$="/auth"]')
                .as('form');

            cy.get('@form')
                .get('[name="shop"]')
                .as('input.shop');

            cy.get('@form')
                .get('[type="submit"]')
                .click();

            cy.get('[name="shop"]:invalid')
                .should('have.length', 1)
                .then(($input) => {
                    expect("Please fill in this field.").to.eq($input[0].validationMessage)
                })
        });
    });

    describe('user', function () {
        beforeEach(() => {
            cy.login();
        });

        it('can see home page', () => {
            cy.visit('/')
                .contains('Home')
                .assertRedirect('/');
            });

        it('Env variables are set', () => {
            cy.visit('/');

            cy.window().then((win) => {
               const expectedKeys = [
                   "env",
                   "base_url",
                   "csrfToken",
                   "shopify.appbridge_enabled",
                   "shopify.appbridge_version",
                   "shopify.app_name",
                   "shopify.api_key",
                   "shopify.api_redirect",
                   "shopify.app_slug",
                   "shop.shopify_domain",
                   "force_redirect",
               ];

               Object.values(expectedKeys).forEach(key => {
                   const value = key.split('.').reduce((a, b) => a[b], win.Env);
                   expect(value).to.not.undefined
               });
            });
        });
    });
})
