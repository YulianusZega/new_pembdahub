describe("Admin Academic Years", () => {
    it("Visits academic years and sees list", () => {
        cy.visit("/login");
        // Note: Configure test credentials or seed a test user for Cypress
        cy.get("input[name=email]").type("superadmin@pembdahub.com");
        cy.get("input[name=password]").type("password");
        cy.get("button[type=submit]").click();

        cy.visit("/admin/academic-years");
        cy.contains("Tahun Ajaran");
    });
});
