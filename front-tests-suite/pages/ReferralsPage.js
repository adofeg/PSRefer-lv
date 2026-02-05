import Base from './core/Base.js';
import { expect } from '@playwright/test';

class ReferralsPage extends Base {
  constructor(page, language = 'es') {
    super(page);
    this.language = language;
    this.pageData = this.loadPageData('referrals', language);
    // Locators defined based on data/referrals/[lang].json structure
  }

  async navigate() {
    await this.page.goto(this.pageData.url);
    await this.page.waitForLoadState('domcontentloaded');
  }

  async verifyListing() {
    const heading = this.page.locator(this.pageData.listing.headingSelector);
    await expect(heading).toBeVisible();
    await expect(heading).toHaveText(this.pageData.listing.headingText);
  }

  async openCreateModal() {
    const createBtn = this.page.locator(this.pageData.create.buttonSelector);
    await createBtn.click();
    await expect(this.page.locator(this.pageData.create.modalSelector)).toBeVisible();
  }

  async submitReferral(data) {
    await this.page.fill(this.pageData.create.form.nameSelector, data.name);
    await this.page.fill(this.pageData.create.form.emailSelector, data.email);
    // Add other fields as necessary based on actual app
    await this.page.click(this.pageData.create.form.submitSelector);
  }

  async verifySuccessMessage() {
     const successMsg = this.page.locator(this.pageData.messages.successSelector);
     await expect(successMsg).toBeVisible();
     await expect(successMsg).toContainText(this.pageData.messages.successText);
  }
}

export default ReferralsPage;
