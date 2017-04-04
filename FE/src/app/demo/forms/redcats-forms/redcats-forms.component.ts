import { Component, OnInit } from '@angular/core';
import { AdminService } from './admin.service';
import {typeDTO} from "../../../common/class/type-dto.class";
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
  public types: typeDTO[];
  public isCreate: boolean;
  public updatingType: typeDTO;

  constructor(private adminService: AdminService) {}

  ngOnInit() {
    this.adminService.getTypes()
      .subscribe((types) => {
        this.types = types
      })
  }

  public saveForm() {
    if (this.isCreate) {
      this.createNewType();
    } else if (this.updatingType) {
      this.updateType();
    }
  }

  public createType() {
    this.isCreate = true;
    this.updatingType = new typeDTO();
  }

   public editType(type: typeDTO) {
    this.updatingType = type;
    this.typename = this.updatingType.type_name;
    this.template = this.updatingType.type_template;
    this.additionalTextField = this.updatingType.type_add_text_fields;
    this.additionalImageField = this.updatingType.type_add_img_fields;
  }

  public resetForm() {
    this.isCreate = false;
    this.updatingType = null;
    this.typename = null;
    this.template = null;
    this.additionalTextField = null;
    this.additionalImageField = null;
  }

  private createNewType() {
    const formData:any = {
      type_name: this.typename,
      template: this.template,
      additionalTextField: this.additionalTextField,
      additionalImgField: this.additionalImageField
    };

    const postForm =  this.adminService.createType(formData).subscribe(
      () => {
        postForm.unsubscribe();
      },
      (error) => {
        console.log(error);
      }
    )
  }

  private updateType() {
    const putSub = this.adminService.updateType(this.updatingType.type_id, this.updatingType)
      .subscribe(
        () => {
          console.log(`update type id: ${this.updatingType.type_id}`);
          putSub.unsubscribe();
        },
        (error) => {
          console.error("something goes wrong");
          console.error(error);
        },
        );
  }
}
