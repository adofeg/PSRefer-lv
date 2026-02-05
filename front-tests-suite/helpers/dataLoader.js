import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export const SUPPORTED_LANGUAGES = ['es'];

export function loadTestData(pageName, languages = SUPPORTED_LANGUAGES) {
  const data = {};
  const loadedLanguages = [];

  languages.forEach(lang => {
    const filePath = path.join(__dirname, '..', 'data', pageName, `${lang}.json`);
    if (fs.existsSync(filePath)) {
      data[lang] = JSON.parse(fs.readFileSync(filePath, 'utf-8'));
      loadedLanguages.push(lang);
    }
  });

  return { data, loadedLanguages };
}
