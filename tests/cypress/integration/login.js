/// <reference types="Cypress" />

describe('Connexion', () => {
    beforeEach( () => {
        cy.visit('/user/prompt')
    })

    it('Connexion', () => {
        cy.get('#userName').type('superadmin')
        cy.get('#password').type('superadmin').type('{enter}')
        cy.url().should('contain', '/')
    })
})