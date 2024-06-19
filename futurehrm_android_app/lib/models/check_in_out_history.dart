import 'package:intl/intl.dart';

class CheckInOutHistory {
  DateTime? checkInDay;
  int? departmentId;
  String? ot;
  String? imageCheckOut;
  DateTime? checkOut;
  double? multiply;
  Duration? outSoon;
  Duration? inLateDiff;
  Duration? time;
  DateTime? checkInDayYMD;
  DateTime? checkInRand;
  DateTime? otTime;
  int? specialDateId;
  DateTime? checkInDayNoFormat;
  Duration? inLate;
  int? numberTime;
  DateTime? checkOutRand;
  Duration? outSoonDiff;
  String? imageCheckIn;
  DateTime? checkIn;
  int? dayOfWeek;

  CheckInOutHistory({
    required this.checkInDay,
    required this.departmentId,
    this.ot,
    this.imageCheckOut,
    required this.checkOut,
    required this.multiply,
    this.outSoon,
    this.inLateDiff,
    required this.time,
    required this.checkInDayYMD,
    required this.checkInRand,
    this.otTime,
    this.specialDateId,
    required this.checkInDayNoFormat,
    this.inLate,
    required this.numberTime,
    required this.checkOutRand,
    this.outSoonDiff,
    this.imageCheckIn,
    required this.checkIn,
    required this.dayOfWeek,
  });

  factory CheckInOutHistory.fromMap(Map<String, dynamic> map) {
    final dateFormatter = DateFormat('dd-MM-yyyy');
    final dateTimeFormatter = DateFormat('yyyy-MM-dd HH:mm:ss');
    final timeFormatter = DateFormat('HH:mm:ss');

    return CheckInOutHistory(
      checkInDay: dateFormatter.parse(map['check_in_day']),
      departmentId: map['department_id'],
      ot: map['ot'],
      imageCheckOut:
          map['image_check_out'] != "null" ? map['image_check_out'] : null,
      checkOut: map['check_out'] != null
          ? timeFormatter.parse(map['check_out'])
          : null,
      multiply: double.parse(map['multiply']),
      outSoon: map['out_soon'] != null ? _parseDuration(map['out_soon']) : null,
      inLateDiff: map['in_late_diff'] != null
          ? _parseDuration(map['in_late_diff'])
          : null,
      time: _parseDuration(map['time']),
      checkInDayYMD:
          DateTime.fromMillisecondsSinceEpoch(map['check_in_day_y_m_d']),
      checkInRand: map['check_in_rand'] != null
          ? dateTimeFormatter.parse(map['check_in_rand'])
          : null,
      otTime:
          map['ot_time'] != null ? timeFormatter.parse(map['ot_time']) : null,
      specialDateId: map['special_date_id'],
      checkInDayNoFormat:
          DateTime.fromMillisecondsSinceEpoch(map['check_in_day_no_format']),
      inLate: map['in_late'] != null ? _parseDuration(map['in_late']) : null,
      numberTime: map['number_time'].toInt(),
      checkOutRand: map['check_out_rand'] != null
          ? dateTimeFormatter.parse(map['check_out_rand'])
          : null,
      outSoonDiff: map['out_soon_diff'] != null
          ? _parseDuration(map['out_soon_diff'])
          : null,
      imageCheckIn:
          map['image_check_in'] != "null" ? map['image_check_in'] : null,
      checkIn: timeFormatter.parse(map['check_in']),
      dayOfWeek: map['day_of_week'],
    );
  }

  static Duration _parseDuration(String duration) {
    final parts = duration.split(':');
    final hours = int.parse(parts[0]);
    final minutes = int.parse(parts[1]);
    final seconds = double.parse(parts[2]).floor();
    return Duration(hours: hours, minutes: minutes, seconds: seconds);
  }
}
