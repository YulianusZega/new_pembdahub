describe("Admin Subjects page", () => {
    beforeEach(() => {
        cy.loginAsAdmin();
    });

    it("sees import link", () => {
        cy.visit("/admin/subjects");
        cy.contains("Import CSV");
    });

    it("uploads CSV and imports subjects", () => {
        cy.visit("/admin/subjects/import");

        // attach CSV fixture and submit
        cy.get("input[type=file]").attachFile("subjects.csv");
        cy.get("button[type=submit]").click();

        // toast / success message
        cy.get("#global-toast").should("contain", "Import selesai");

        // verify entries exist on index
        cy.visit("/admin/subjects");
        cy.contains("Matematika");
        cy.contains("Fisika Dasar");
    });
});
