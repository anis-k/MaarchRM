describe('Connexion', () => {
    beforeEach( () => {
        cy.login('bblier', 'maarch')
    })

    it('À propos', () => {
        cy.get('[onclick="displayInformation()"]').click()
    })
})