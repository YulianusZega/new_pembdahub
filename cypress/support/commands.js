// Custom commands for Cypress
import "cypress-file-upload";

// login helper
Cypress.Commands.add("loginAsAdmin", () => {
    cy.visit("/login");
    cy.get("input[name=email]").type("superadmin@pembdahub.com");
    cy.get("input[name=password]").type("password");
    cy.get("button[type=submit]").click();
});
