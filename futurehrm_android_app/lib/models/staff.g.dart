// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'staff.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class StaffAdapter extends TypeAdapter<Staff> {
  @override
  final int typeId = 1;

  @override
  Staff read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return Staff(
      id: fields[0] as int?,
      code: fields[1] as String?,
      firstname: fields[2] as String?,
      lastname: fields[3] as String?,
      department: fields[4] as int?,
      isManager: fields[5] as bool?,
      joinedAt: fields[6] as DateTime?,
      offDate: fields[7] as DateTime?,
      dob: fields[8] as DateTime?,
      gender: fields[9] as int?,
      regional: fields[10] as int?,
      phoneNumber: fields[11] as String?,
      email: fields[12] as String?,
      password: fields[13] as String?,
      idNumber: fields[14] as String?,
      identity_issue_date: fields[15] as DateTime?,
      photo: fields[16] as String?,
      idPhoto: fields[17] as String?,
      idPhotoBack: fields[18] as String?,
      dayOfLeave: fields[19] as double?,
      note: fields[20] as String?,
      createdAt: fields[21] as DateTime?,
      createdBy: fields[22] as int?,
      updatedAt: fields[23] as DateTime?,
      updatedBy: fields[24] as int?,
      status: fields[25] as bool?,
    );
  }

  @override
  void write(BinaryWriter writer, Staff obj) {
    writer
      ..writeByte(26)
      ..writeByte(0)
      ..write(obj.id)
      ..writeByte(1)
      ..write(obj.code)
      ..writeByte(2)
      ..write(obj.firstname)
      ..writeByte(3)
      ..write(obj.lastname)
      ..writeByte(4)
      ..write(obj.department)
      ..writeByte(5)
      ..write(obj.isManager)
      ..writeByte(6)
      ..write(obj.joinedAt)
      ..writeByte(7)
      ..write(obj.offDate)
      ..writeByte(8)
      ..write(obj.dob)
      ..writeByte(9)
      ..write(obj.gender)
      ..writeByte(10)
      ..write(obj.regional)
      ..writeByte(11)
      ..write(obj.phoneNumber)
      ..writeByte(12)
      ..write(obj.email)
      ..writeByte(13)
      ..write(obj.password)
      ..writeByte(14)
      ..write(obj.idNumber)
      ..writeByte(15)
      ..write(obj.identity_issue_date)
      ..writeByte(16)
      ..write(obj.photo)
      ..writeByte(17)
      ..write(obj.idPhoto)
      ..writeByte(18)
      ..write(obj.idPhotoBack)
      ..writeByte(19)
      ..write(obj.dayOfLeave)
      ..writeByte(20)
      ..write(obj.note)
      ..writeByte(21)
      ..write(obj.createdAt)
      ..writeByte(22)
      ..write(obj.createdBy)
      ..writeByte(23)
      ..write(obj.updatedAt)
      ..writeByte(24)
      ..write(obj.updatedBy)
      ..writeByte(25)
      ..write(obj.status);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is StaffAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
