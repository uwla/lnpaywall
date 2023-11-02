export interface NodeInfo {
    synced: boolean,
    pubkey: string,
    onChainBalance: {
        confirmed: number,
        pending: number,
        total: number,
    },
    lightningBalance: {
        available: number,
        notAvailable: number,
        pending: number,
        total: number,
    },
    fees: {
        fastest: number,
        hour: number,
        halfHour: number,
        minimum: number,
    }
}

export interface Channels {
    openChannelsCount: number,
    activeChannelsCount: number,
    inactiveChannelsCount: number,
    pendingChannelsCount: number,
    closedChannelsCount: number,

    totalCapacity: number,
    totalActiveCapacity: number,
    totalInactiveCapacity: number,
    totalPendingCapacity: number,
    totalLocalBalance: number,
    totalRemoteBalance: number,
    activeLocalBalance: number,
    activeRemoteBalance: number,
    inactiveLocalBalance: number,
    inactiveRemoteBalance: number,
    pendingLocalBalance: number,
    pendingRemoteBalance: number,

    channels: Channel[],
}

export interface Channel {
    peer: string,
    status: ChannelStatus,
    capacity: number,
    localBalance: number,
    remoteBalance: number,
    balanceRatio: number,
    channelId?: string,
}

export enum ChannelStatus {
    Active,
    Inactive,
    Pending,
    Closed,
}


export interface ChannelInfo {
    id: string;
    capacity: number;
    policy?: {
        base_fee_mtokens?: string;
        cltv_delta?: number;
        fee_rate?: number;
        is_disabled?: boolean;
        max_htlc_mtokens?: string;
        min_htlc_mtokens?: string;
        public_key: string;
        updated_at?: string;
    }
    transaction_id: string;
    transaction_vout: number;
    updated_at?: string;
}

export interface Peer {
    pubkey: string,
    socket: string,
}

