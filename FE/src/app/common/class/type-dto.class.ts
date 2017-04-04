export class typeDTO {
  type_id: string;
  type_name: string;
  type_template: string;
  type_add_text_fields: string;
  type_add_img_fields: string;

  constructor(type?: ({
    type_id: string,
    type_name: string,
    type_template: string,
    type_add_text_fields: string,
    type_add_img_fields: string
  })) {
    if (type) {
      this.type_id = type.type_id;
      this.type_name = type.type_name;
      this.type_template = type.type_template;
      this.type_add_text_fields = type.type_add_text_fields;
      this.type_add_img_fields = type.type_add_img_fields;
    }
  }
}
