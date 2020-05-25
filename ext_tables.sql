CREATE TABLE tx_in2bemail_domain_model_mailing
(
    uid                           int(11)                            NOT NULL auto_increment,
    pid                           int(11)             DEFAULT '0'    NOT NULL,

    be_groups                     int(11)             DEFAULT '0'    NOT NULL,
    fe_groups                     int(11)             DEFAULT '0'    NOT NULL,
    context                       varchar(255)        DEFAULT 'fe'   NOT NULL,
    subject                       text                               NOT NULL,
    bodytext                      mediumtext                         NOT NULL,
    mail_format                   varchar(255)        DEFAULT 'both' NOT NULL,
    sender_mail                   varchar(255)        DEFAULT ''     NOT NULL,
    sender_name                   varchar(255)        DEFAULT ''     NOT NULL,
    mail_queue_generated          tinyint(4) unsigned DEFAULT '0'    NOT NULL,
    workflow_state                int(11) unsigned    DEFAULT '1'    NOT NULL,
    workflow_status_change_tstamp int(11) unsigned    DEFAULT '0'    NOT NULL,
    rejected                      tinyint(4) unsigned DEFAULT '0'    NOT NULL,
    attachments                   int(11)             DEFAULT '0'    NOT NULL,

    tstamp                        int(11) unsigned    DEFAULT '0'    NOT NULL,
    crdate                        int(11) unsigned    DEFAULT '0'    NOT NULL,
    cruser_id                     int(11) unsigned    DEFAULT '0'    NOT NULL,
    deleted                       tinyint(4) unsigned DEFAULT '0'    NOT NULL,
    hidden                        tinyint(4) unsigned DEFAULT '0'    NOT NULL,
    starttime                     int(11) unsigned    DEFAULT '0'    NOT NULL,
    endtime                       int(11) unsigned    DEFAULT '0'    NOT NULL,

    sys_language_uid              int(11)             DEFAULT '0'    NOT NULL,
    l10n_parent                   int(11)             DEFAULT '0'    NOT NULL,
    l10n_diffsource               mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY language (l10n_parent, sys_language_uid)
);

CREATE TABLE tx_in2bemail_domain_model_mailqueue
(
    uid              int(11)                          NOT NULL auto_increment,
    pid              int(11)             DEFAULT '0'  NOT NULL,

    mailing          int(11)             DEFAULT '0'  NOT NULL,
    be_user          int(11)             DEFAULT '0'  NOT NULL,
    fe_user          int(11)             DEFAULT '0'  NOT NULL,
    context          varchar(255)        DEFAULT 'fe' NOT NULL,
    sent             tinyint(4) unsigned DEFAULT '0'  NOT NULL,
    error            tinyint(4) unsigned DEFAULT '0'  NOT NULL,

    tstamp           int(11) unsigned    DEFAULT '0'  NOT NULL,
    crdate           int(11) unsigned    DEFAULT '0'  NOT NULL,
    cruser_id        int(11) unsigned    DEFAULT '0'  NOT NULL,
    deleted          tinyint(4) unsigned DEFAULT '0'  NOT NULL,
    hidden           tinyint(4) unsigned DEFAULT '0'  NOT NULL,
    starttime        int(11) unsigned    DEFAULT '0'  NOT NULL,
    endtime          int(11) unsigned    DEFAULT '0'  NOT NULL,

    sys_language_uid int(11)             DEFAULT '0'  NOT NULL,
    l10n_parent      int(11)             DEFAULT '0'  NOT NULL,
    l10n_diffsource  mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY language (l10n_parent, sys_language_uid)
);

CREATE TABLE tx_in2bemail_log
(
    request_id varchar(13)         DEFAULT ''  NOT NULL,
    time_micro double(16, 4)                   NOT NULL default '0.0000',
    component  varchar(255)        DEFAULT ''  NOT NULL,
    level      tinyint(1) unsigned DEFAULT '0' NOT NULL,
    message    text,
    data       text,

    KEY request (request_id)
);

CREATE TABLE tx_in2bemail_mailing_be_groups_mm
(
    uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
    sorting         int(11) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_in2bemail_mailing_fe_groups_mm
(
    uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
    sorting         int(11) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
