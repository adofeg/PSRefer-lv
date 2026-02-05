import Base from './core/Base.js';
import { expect } from '@playwright/test';

class RegisterPage extends Base {
  constructor(page, language = 'es') {
    super(page);
    this.language = language;
    this.pageData = this.loadPageData('register', language);

    this.nameInput = page.locator(this.pageData.name.selector);
    this.emailInput = page.locator(this.pageData.email.selector);
    this.passwordInput = page.locator(this.pageData.password.selector);
    this.passwordConfirmationInput = page.locator(this.pageData.passwordConfirmation.selector);
    this.submitButton = page.locator(this.pageData.submitButton.selector);
  }

  async navigate() {
    await super.navigate(this.pageData.url);
  }

  async register() {
    await this.nameInput.fill(this.pageData.name.value);
    const uniqueEmail = `test_${Date.now()}@example.com`;
    await this.emailInput.fill(uniqueEmail);
    await this.passwordInput.fill(this.pageData.password.value);
    await this.passwordConfirmationInput.fill(this.pageData.passwordConfirmation.value);
    await this.submitButton.click();
  }

  async verifySuccess() {
    await expect(this.page).toHaveURL(new RegExp(this.pageData.validation.successUrl));
  }
}

export default RegisterPage;
