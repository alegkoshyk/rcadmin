import { Component, OnInit } from '@angular/core';
import { AdminService } from './admin.service';
@Component({
  selector: 'app-redcats-forms',
  templateUrl: './redcats-forms.component.html',
  styleUrls: ['./redcats-forms.component.css']
})
export class RedcatsFormsComponent implements OnInit {
  public typename: string;
  public template: string;
  public additionalTextField: string;
  public additionalImageField: string;

  constructor(private adminService: AdminService) {}

  ngOnInit() {
    this.typename = 'First Type';
    this.template = 'Some Template';
    this.additionalTextField = '';
    this.additionalImageField = '';
  }

  public saveForm() {
    const formData:any = {
      type_name: this.typename,
      template: this.template,
      additionalTextField: this.additionalTextField,
      additionalImgField: this.additionalImageField
    };

    const postForm =  this.adminService.postForm(formData).subscribe(
      (type) => {
        console.log(type);
        postForm.unsubscribe();
      },
      (error) => {
        console.log(error);
      }
    )
  }

}
