// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'check_in_out.dart';

// **************************************************************************
// TypeAdapterGenerator
// **************************************************************************

class CheckInOutAdapter extends TypeAdapter<CheckInOut> {
  @override
  final int typeId = 2;

  @override
  CheckInOut read(BinaryReader reader) {
    final numOfFields = reader.readByte();
    final fields = <int, dynamic>{
      for (int i = 0; i < numOfFields; i++) reader.readByte(): reader.read(),
    };
    return CheckInOut(
      id: fields[0] as int?,
      staffId: fields[1] as int,
      staffCode: fields[2] as String,
      checkInDay: fields[3] as DateTime,
      checkInAt: fields[4] as DateTime,
      type: fields[5] as bool,
      image: fields[6] as String?,
    );
  }

  @override
  void write(BinaryWriter writer, CheckInOut obj) {
    writer
      ..writeByte(7)
      ..writeByte(0)
      ..write(obj.id)
      ..writeByte(1)
      ..write(obj.staffId)
      ..writeByte(2)
      ..write(obj.staffCode)
      ..writeByte(3)
      ..write(obj.checkInDay)
      ..writeByte(4)
      ..write(obj.checkInAt)
      ..writeByte(5)
      ..write(obj.type)
      ..writeByte(6)
      ..write(obj.image);
  }

  @override
  int get hashCode => typeId.hashCode;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is CheckInOutAdapter &&
          runtimeType == other.runtimeType &&
          typeId == other.typeId;
}
