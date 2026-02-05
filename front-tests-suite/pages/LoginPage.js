import Base from './core/Base.js';
import { expect } from '@playwright/test';

class LoginPage extends Base {
  constructor(page, language = 'es') {
    super(page);
    this.language = language;
    this.pageData = this.loadPageData('login', language);

    this.emailInput = page.locator(this.pageData.email.selector);
    this.passwordInput = page.locator(this.pageData.password.selector);
    this.submitButton = page.locator(this.pageData.submitButton.selector);
  }

  async navigate() {
    await super.navigate(this.pageData.url);
  }

  async login(email, password) {
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
    await this.submitButton.click();
  }

  async verifySuccess() {
    await expect(this.page).toHaveURL(new RegExp(this.pageData.validation.successUrl));
  }
}

export default LoginPage;
