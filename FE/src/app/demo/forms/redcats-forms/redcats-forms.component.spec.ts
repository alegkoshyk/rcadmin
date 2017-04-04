import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RedcatsFormsComponent } from './redcats-forms.component';

describe('RedcatsFormsComponent', () => {
  let component: RedcatsFormsComponent;
  let fixture: ComponentFixture<RedcatsFormsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RedcatsFormsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RedcatsFormsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
