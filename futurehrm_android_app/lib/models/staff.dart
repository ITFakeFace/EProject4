import 'dart:core';

import 'package:hive/hive.dart';

part 'staff.g.dart';

@HiveType(typeId: 1)
class Staff {
  @HiveField(0)
  int? id;
  @HiveField(1)
  String? code;
  @HiveField(2)
  String? firstname;
  @HiveField(3)
  String? lastname;
  @HiveField(4)
  int? department;
  @HiveField(5)
  bool? isManager;
  @HiveField(6)
  DateTime? joinedAt;
  @HiveField(7)
  DateTime? offDate;
  @HiveField(8)
  DateTime? dob;
  @HiveField(9)
  int? gender;
  @HiveField(10)
  int? regional;
  @HiveField(11)
  String? phoneNumber;
  @HiveField(12)
  String? email;
  @HiveField(13)
  String? password;
  @HiveField(14)
  String? idNumber;
  @HiveField(15)
  DateTime? identity_issue_date;
  @HiveField(16)
  String? photo;
  @HiveField(17)
  String? idPhoto;
  @HiveField(18)
  String? idPhotoBack;
  @HiveField(19)
  double? dayOfLeave;
  @HiveField(20)
  String? note;
  @HiveField(21)
  DateTime? createdAt;
  @HiveField(22)
  int? createdBy;
  @HiveField(23)
  DateTime? updatedAt;
  @HiveField(24)
  int? updatedBy;
  @HiveField(25)
  bool? status;

  Staff({
    this.id,
    this.code,
    this.firstname,
    this.lastname,
    this.department,
    this.isManager,
    this.joinedAt,
    this.offDate,
    this.dob,
    this.gender,
    this.regional,
    this.phoneNumber,
    this.email,
    this.password,
    this.idNumber,
    this.identity_issue_date,
    this.photo,
    this.idPhoto,
    this.idPhotoBack,
    this.dayOfLeave,
    this.note,
    this.createdAt,
    this.createdBy,
    this.updatedAt,
    this.updatedBy,
    this.status,
  });

  factory Staff.fromMap(Map<String, dynamic> json) {
    return Staff(
      id: json['id'],
      code: json['code'],
      firstname: json['firstname'],
      lastname: json['lastname'],
      department: json['department'],
      isManager: json['isManager'],
      joinedAt: json['joinedAt'] != null
          ? DateTime.fromMillisecondsSinceEpoch(json['joinedAt'])
          : null,
      offDate: json['offDate'] != null
          ? DateTime.fromMillisecondsSinceEpoch(json['offDate'])
          : null,
      dob: json['dob'] != null
          ? DateTime.fromMillisecondsSinceEpoch(json['dob'])
          : null,
      gender: json['gender'],
      regional: json['regional'],
      phoneNumber: json['phoneNumber'],
      email: json['email'],
      password: json['password'],
      idNumber: json['idNumber'],
      identity_issue_date: json['identity_issue_date'] != null
          ? DateTime.fromMillisecondsSinceEpoch(json['identity_issue_date'])
          : null,
      photo: json['photo'],
      idPhoto: json['idPhoto'],
      idPhotoBack: json['idPhotoBack'],
      dayOfLeave: json['dayOfLeave'],
      note: json['note'],
      createdAt: json['createdAt'] != null
          ? DateTime.fromMillisecondsSinceEpoch(json['createdAt'])
          : null,
      createdBy: json['createdBy'],
      updatedAt: json['updatedAt'] != null
          ? DateTime.fromMillisecondsSinceEpoch(json['updatedAt'])
          : null,
      updatedBy: json['updatedBy'],
      status: json['status'],
    );
  }

  Map<String, dynamic> toMap() {
    return {
      "id": id,
      "code": code,
      "firstname": firstname,
      "lastname": lastname,
      "department": department,
      "isManager": isManager,
      "joinedAt": joinedAt?.toIso8601String(),
      "offDate": offDate?.toIso8601String(),
      "dob": dob?.toIso8601String(),
      "gender": gender,
      "regional": regional,
      "phoneNumber": phoneNumber,
      "email": email,
      "password": password,
      "idNumber": idNumber,
      "identity_issue_date": identity_issue_date?.toIso8601String(),
      "photo": photo,
      "idPhoto": idPhoto,
      "idPhotoBack": idPhotoBack,
      "dayOfLeave": dayOfLeave,
      "note": note,
      "createdAt": createdAt?.toIso8601String(),
      "createdBy": createdBy,
      "updatedAt": updatedAt?.toIso8601String(),
      "updatedBy": updatedBy,
      "status": status,
    };
  }
}
