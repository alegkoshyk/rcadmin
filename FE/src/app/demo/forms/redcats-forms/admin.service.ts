import {Injectable} from '@angular/core';
import {Http} from "@angular/http";
import {Observable} from "rxjs";
import {typeDTO}from '../../../common/class/type-dto.class'

const typePath = 'http://localhost:80/rcadmin/web/index.php/pages/api/types';
const mockedTypesJson = `[
    {
      "type_id":"10",
      "type_name":"test",
      "type_template":"templ1",
      "type_add_text_fields":"color size width",
      "datetime":"0000-00-00 00:00:00",
      "type_add_img_fields":"main second icon"
    },
    {
      "type_id":"11",
      "type_name":"foffofo",
      "type_template":"templ1",
      "type_add_text_fields":"tedt1 ihikhik gjknkj",
      "datetime":"0000-00-00 00:00:00",
      "type_add_img_fields":"hgjh kjlk"
    }
  ]`;


@Injectable()
export class AdminService {

  constructor(private http: Http) {}

  public createType(formData: any) {
    return this.http.post(typePath, formData);
  }

  public getTypes(): Observable<typeDTO[]> {
    const types = JSON.parse(mockedTypesJson)
      .map(type => new typeDTO(type));

    return Observable.of(types);
  }

  public updateType(id: string, type: typeDTO) {
    return this.http.put(`${typePath}/${id}`, type);
  }

}
