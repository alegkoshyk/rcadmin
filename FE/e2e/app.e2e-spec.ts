import { RcadminPage } from './app.po';

describe('rcadmin App', () => {
  let page: RcadminPage;

  beforeEach(() => {
    page = new RcadminPage();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
