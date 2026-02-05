import Base from './core/Base.js';
import { expect } from '@playwright/test';

class OfferingsPage extends Base {
  constructor(page, language = 'es') {
    super(page);
    this.language = language;
    this.pageData = this.loadPageData('offerings', language);

    this.catalogTitle = page.locator(this.pageData.catalogTitle.selector);
    this.firstOfferingCard = page.locator(this.pageData.firstOfferingCard.selector);
    this.referButton = page.locator(this.pageData.referButton.selector);
  }

  async navigate() {
    await super.navigate(this.pageData.url);
  }

  async verifyCatalogVisible() {
    await expect(this.catalogTitle).toHaveText(this.pageData.catalogTitle.text);
  }

  async createOffering() {
      // Logic to create offering, e.g. click button, fill form (if mocked data needs it)
      // Since we mock the POST, we just need to trigger it.
      // Assuming there is a button "New Offering"
      const createBtn = this.page.locator(this.pageData.create.buttonSelector);
      // Only if visible (mock might be for admin only?)
      // We assume strict mode: it must be visible.
      await createBtn.click();

      // If modal opens
      const modal = this.page.locator(this.pageData.create.modalSelector);
      await expect(modal).toBeVisible();

      // Submit
      await this.page.click(this.pageData.create.submitSelector);
  }
}

export default OfferingsPage;
